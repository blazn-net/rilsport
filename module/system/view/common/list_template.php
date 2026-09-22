<?php
/**
 * Template Parent Universel pour les Pages List (RIL Sport)
 * 
 * Centralise l'en-tête, les alertes flash, la barre de recherche standardisée,
 * la structure du tableau responsive Metro UI, ainsi que la visibilité admin
 * des colonnes "Statut" et "Actions" (Modifier, Désactiver, Réactiver, Supprimer).
 * 
 * $config attendu :
 * - title          : Titre de la page (ex: 'Saisons')
 * - icon           : Icône Metro UI (ex: 'mif-calendar')
 * - addUrl         : URL du bouton d'ajout (ex: URLROOT . '/sport/season')
 * - addBtnText     : Libellé du bouton (par défaut: '+ Ajouter')
 * - searchFields   : Champs filtrés par la recherche Metro UI (ex: 'code,name')
 * - customFiltersHtml : HTML optionnel de filtres supplémentaires au-dessus du tableau
 * - columns        : Définition des colonnes du tableau
 * - items          : Tableau de données
 * - idField        : Nom de la clé d'ID (défaut: 'id')
 * - statusField    : Nom du champ statut (défaut: 'status_id')
 * - actions        : URLs d'actions avec placeholder {id} ('editUrl', 'disableUrl', 'activateUrl', 'deleteUrl')
 */
$config = $listConfig ?? $data['listConfig'] ?? [];
?>
<main class="p-4" style="margin-top: 60px;">
    <!-- En-tête : Titre & Bouton "+ Ajouter" -->
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2>
            <?php if (!empty($config['icon'])): ?>
                <span class="<?php echo htmlspecialchars($config['icon']); ?> mr-2"></span>
            <?php endif; ?>
            <?php echo htmlspecialchars($config['title'] ?? ''); ?>
        </h2>

        <?php if (!empty($data['isAdmin']) && !empty($config['addUrl'])): ?>
            <a href="<?php echo htmlspecialchars($config['addUrl']); ?>" 
               class="button info mt-2 mt-md-0" 
               title="<?php echo htmlspecialchars($config['addBtnText'] ?? $data['txt']['SYS_BTN_ADD'] ?? 'Ajouter'); ?>">
                <span class="mif-plus"></span> <span class="btn-text"><?php echo htmlspecialchars($config['addBtnText'] ?? $data['txt']['SYS_BTN_ADD'] ?? 'Ajouter'); ?></span>
            </a>
        <?php endif; ?>
    </div>

    <!-- Alertes Flash -->
    <?php if (!empty($data['message'])): ?>
        <div class="remark success mb-3"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert mb-3"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <!-- Filtres Personnalisés (si présents) -->
    <?php if (!empty($config['customFiltersHtml'])): ?>
        <div class="mb-3">
            <?php echo $config['customFiltersHtml']; ?>
        </div>
    <?php endif; ?>

    <!-- Tableau de Données Responsive -->
    <div class="table-scroll-wrapper">
    <table class="table striped table-border mt-4 w-100" 
           data-role="table"
           data-horizontal-scroll="true"
           data-show-search="<?php echo ($config['showSearch'] ?? true) ? 'true' : 'false'; ?>"
           data-search-placeholder="<?php echo htmlspecialchars($config['searchPlaceholder'] ?? $data['txt']['SYS_SEARCH_PLACEHOLDER'] ?? 'Rechercher...'); ?>"
           data-show-rows-steps="false" 
           data-check="false" 
           data-rownum="false"
           <?php if (!empty($config['searchFields'])): ?>
           data-search-fields="<?php echo htmlspecialchars($config['searchFields']); ?>"
           <?php endif; ?>>
        <thead>
            <tr>
                <?php foreach ($config['columns'] as $col): ?>
                    <th <?php if (!empty($col['field'])): ?>data-name="<?php echo htmlspecialchars($col['field']); ?>"<?php endif; ?>
                        <?php if (!empty($col['headerStyle'])): ?>style="<?php echo htmlspecialchars($col['headerStyle']); ?>"<?php endif; ?>>
                        <?php echo htmlspecialchars($col['label']); ?>
                    </th>
                <?php endforeach; ?>

                <?php if (!empty($data['isAdmin'])): ?>
                    <th><?php echo htmlspecialchars($data['txt']['SYS_COL_STATUS'] ?? 'Statut'); ?></th>
                    <th><?php echo htmlspecialchars($data['txt']['SYS_COL_ACTIONS'] ?? 'Actions'); ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($config['items'])): ?>
                <?php foreach ($config['items'] as $item): 
                    $row = is_object($item) ? (array)$item : $item;
                    $id = $row[$config['idField'] ?? 'id'] ?? '';
                    $statusId = isset($row[$config['statusField'] ?? 'status_id']) ? (int)$row[$config['statusField'] ?? 'status_id'] : 1;
                    $isActive = ($statusId === 1);
                    $statusText = $isActive ? ($data['txt']['SPORT_ACTIVE'] ?? $data['txt']['SYS_ACTIVE'] ?? 'Actif') : ($data['txt']['SPORT_INACTIVE'] ?? $data['txt']['SYS_INACTIVE'] ?? 'Inactif');
                    $badgeClass = $isActive ? 'success' : 'secondary';
                ?>
                    <tr>
                        <?php foreach ($config['columns'] as $col): 
                            $field = $col['field'] ?? '';
                            $val = $row[$field] ?? '';
                            $label = $col['label'] ?? '';
                            $type = $col['type'] ?? 'text';
                        ?>
                            <td data-label="<?php echo htmlspecialchars($label); ?>">
                                <?php 
                                if (isset($col['render']) && is_callable($col['render'])) {
                                    echo call_user_func($col['render'], $val, $row, $data);
                                } elseif ($type === 'code') {
                                    echo '<code>' . htmlspecialchars((string)$val) . '</code>';
                                } elseif ($type === 'link') {
                                    $linkUrl = isset($col['linkUrl']) ? str_replace('{id}', htmlspecialchars((string)$id), $col['linkUrl']) : '#';
                                    $linkTitle = $col['linkTitle'] ?? 'Consulter la fiche';
                                    echo '<a href="' . $linkUrl . '" class="text-bold fg-dark" title="' . htmlspecialchars($linkTitle) . '">' . htmlspecialchars((string)$val) . '</a>';
                                } elseif ($type === 'date') {
                                    echo !empty($val) ? htmlspecialchars(date($col['dateFormat'] ?? 'd/m/Y', strtotime($val))) : '-';
                                } elseif ($type === 'badge') {
                                    $bClass = $col['badgeClass'] ?? 'info';
                                    echo '<span class="badge ' . $bClass . '">' . htmlspecialchars((string)$val) . '</span>';
                                } else {
                                    echo htmlspecialchars((string)$val);
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>

                        <?php if (!empty($data['isAdmin'])): ?>
                            <!-- Colonne Statut (Admin uniquement) -->
                            <td data-label="<?php echo htmlspecialchars($data['txt']['SYS_COL_STATUS'] ?? 'Statut'); ?>">
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($statusText); ?></span>
                            </td>

                            <!-- Colonne Actions (Admin uniquement) -->
                            <td data-label="<?php echo htmlspecialchars($data['txt']['SYS_COL_ACTIONS'] ?? 'Actions'); ?>">
                                <div class="d-flex flex-row flex-wrap" style="gap: 5px;">
                                    <?php 
                                    $actions = $config['actions'] ?? [];
                                    
                                    // 1. Modifier (Admin)
                                    if (!empty($actions['editUrl'])): 
                                        $editUrl = str_replace('{id}', htmlspecialchars((string)$id), $actions['editUrl']);
                                    ?>
                                        <a href="<?php echo $editUrl; ?>" class="button small info" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'); ?>">
                                            <span class="mif-pencil"></span>
                                        </a>
                                    <?php endif; ?>

                                    <?php 
                                    // 2. Désactiver (si actif, Admin)
                                    if ($isActive && !empty($actions['disableUrl'])): 
                                        $disableUrl = str_replace('{id}', htmlspecialchars((string)$id), $actions['disableUrl']);
                                        $confirmMsg = $actions['disableConfirm'] ?? ($data['txt']['SYS_CONFIRM_DISABLE'] ?? 'Voulez-vous vraiment désactiver cet élément ?');
                                    ?>
                                        <button type="button" 
                                                onclick="if(confirm('<?php echo addslashes($confirmMsg); ?>')) location.href='<?php echo $disableUrl; ?>';" 
                                                class="button small warning" 
                                                title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_DISABLE'] ?? 'Désactiver'); ?>">
                                            <span class="mif-cancel"></span>
                                        </button>
                                    <?php endif; ?>

                                    <?php 
                                    // 3. Réactiver (si inactif, Admin)
                                    if (!$isActive && !empty($actions['activateUrl'])): 
                                        $activateUrl = str_replace('{id}', htmlspecialchars((string)$id), $actions['activateUrl']);
                                        $confirmMsg = $actions['activateConfirm'] ?? ($data['txt']['SYS_CONFIRM_ACTIVATE'] ?? 'Voulez-vous vraiment réactiver cet élément ?');
                                    ?>
                                        <button type="button" 
                                                onclick="if(confirm('<?php echo addslashes($confirmMsg); ?>')) location.href='<?php echo $activateUrl; ?>';" 
                                                class="button small success" 
                                                title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_ACTIVATE'] ?? 'Réactiver'); ?>">
                                            <span class="mif-checkmark"></span>
                                        </button>
                                    <?php endif; ?>

                                    <?php 
                                    // 4. Supprimer définitivement (Admin)
                                    if (!empty($actions['deleteUrl'])): 
                                        $deleteUrl = str_replace('{id}', htmlspecialchars((string)$id), $actions['deleteUrl']);
                                        $confirmMsg = $actions['deleteConfirm'] ?? ($data['txt']['SYS_CONFIRM_DELETE'] ?? 'Voulez-vous vraiment supprimer définitivement cet élément ?');
                                    ?>
                                        <button type="button" 
                                                onclick="if(confirm('<?php echo addslashes($confirmMsg); ?>')) location.href='<?php echo $deleteUrl; ?>';" 
                                                class="button small alert" 
                                                title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_DELETE'] ?? 'Supprimer'); ?>">
                                            <span class="mif-bin"></span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?php echo count($config['columns']) + (!empty($data['isAdmin']) ? 2 : 0); ?>" class="text-center p-4 fg-gray">
                        Aucun enregistrement trouvé.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</main>

