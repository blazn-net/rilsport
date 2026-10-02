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
<main class="list-page" style="padding-top: 68px;">
    <!-- En-tête : Titre & Bouton "+ Ajouter" -->
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4" style="flex-shrink: 0;">
        <h2>
            <?php if (!empty($config['icon'])): ?>
                <span class="<?php echo htmlspecialchars($config['icon']); ?> mr-2"></span>
            <?php endif; ?>
            <?php echo htmlspecialchars($config['title'] ?? ''); ?>
        </h2>

        <?php if (!empty($data['isAdmin']) && !empty($config['addUrl'])): ?>
            <a href="<?php echo htmlspecialchars($config['addUrl']); ?>"
               class="button info mt-2 mt-md-0"
               id="btn-add-main"
               title="<?php echo htmlspecialchars($config['addBtnText'] ?? $data['txt']['SYS_BTN_ADD'] ?? 'Ajouter'); ?>">
                <span class="mif-plus"></span>
                <span class="btn-text"><?php echo htmlspecialchars($data['txt']['SYS_BTN_ADD'] ?? 'Ajouter'); ?></span>
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

    <?php
    // ID unique pour cette instance de table (permet plusieurs tables sur une même page sans collision).
    static $listTemplateInstance = 0;
    $listTemplateInstance++;
    $paginationWrapperId = 'list-pagination-' . $listTemplateInstance;

    // Recherche via GET (persiste dans l'URL) ou filtres personnalisés ?
    $listUrl        = $config['listUrl']    ?? '';        // ex: URLROOT . '/sport/seasons'
    $searchValue    = $config['searchValue'] ?? '';       // valeur courante de ?search=
    $showGetSearch  = ($listUrl !== '') && ($config['showSearch'] ?? true);
    $hasAnyFilter   = $showGetSearch || !empty($config['customFiltersHtml']);
    ?>

    <!-- Filtres (personnalisés + recherche GET), regroupés dans une zone repliable -->
    <?php if ($hasAnyFilter): ?>
        <?php $filterSectionId = 'list-filter-section-' . $listTemplateInstance; ?>
        <div class="list-filter-section mb-3" id="<?php echo $filterSectionId; ?>" style="flex-shrink: 0;">
            <div class="list-filter-summary"
                 onclick="event.preventDefault(); event.stopPropagation(); document.getElementById('<?php echo $filterSectionId; ?>').classList.toggle('collapsed'); return false;">
                <span class="list-filter-arrow">▾</span>
                <?php echo htmlspecialchars($data['txt']['SYS_FILTERS'] ?? 'Filtres'); ?>
            </div>
            <div class="list-filter-body">
                <?php if ($showGetSearch): ?>
                <form method="GET" action="<?php echo htmlspecialchars($listUrl); ?>" class="d-flex flex-row flex-wrap flex-align-end" style="gap: 10px;">
                    <div style="flex: 2; min-width: 200px;">
                        <label class="text-bold d-block">
                            <span class="mif-search mr-1"></span>
                            <?php echo htmlspecialchars($data['txt']['SYS_SEARCH'] ?? 'Recherche'); ?>
                        </label>
                        <input type="text" name="search" data-role="input"
                               placeholder="<?php echo htmlspecialchars($config['searchPlaceholder'] ?? $data['txt']['SYS_SEARCH_PLACEHOLDER'] ?? 'Rechercher...'); ?>"
                               value="<?php echo htmlspecialchars($searchValue); ?>">
                    </div>

                    <div style="flex: 0 0 auto; padding-bottom: 2px;">
                        <button type="submit" class="button primary mr-1">
                            <span class="mif-filter"></span>
                            <span class="btn-text"><?php echo htmlspecialchars($data['txt']['SYS_BTN_FILTER'] ?? 'Filtrer'); ?></span>
                        </button>
                        <a href="<?php echo htmlspecialchars($listUrl); ?>" class="button secondary">
                            <span class="mif-reload"></span>
                            <span class="btn-text"><?php echo htmlspecialchars($data['txt']['SYS_BTN_RESET'] ?? 'Réinitialiser'); ?></span>
                        </a>
                    </div>
                </form>
                <?php endif; ?>

                <?php if (!empty($config['customFiltersHtml'])): ?>
                    <?php echo $config['customFiltersHtml']; ?>
                <?php endif; ?>
            </div>

        </div>
        <style>
            .list-filter-section { background: #fff; border: 1px solid #e3e3e3; border-radius: 6px; }
            .list-filter-summary { padding: 10px 14px; font-size: 13px; font-weight: 600; color: #444; cursor: pointer; user-select: none; border-bottom: 1px solid #eee; }
            .list-filter-arrow { display: inline-block; font-size: 11px; color: #999; transition: transform .15s; }
            .list-filter-section.collapsed .list-filter-arrow { transform: rotate(-90deg); }
            .list-filter-section.collapsed .list-filter-summary { border-bottom: none; }
            .list-filter-body { padding: 12px 14px; }
            .list-filter-section.collapsed .list-filter-body { display: none; }
        </style>
    <?php endif; ?>

    <!-- Tableau de Données Responsive -->
    <div class="table-scroll-wrapper">
    <table class="table striped table-border mt-4 w-100" 
           data-role="table"
           data-horizontal-scroll="true"
           data-show-search="false"
           data-pagination-wrapper="#<?php echo $paginationWrapperId; ?>"
           data-info-wrapper="#<?php echo $paginationWrapperId; ?>"
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
                                    echo '<a href="' . $linkUrl . '" class="text-bold fg-primary" title="' . htmlspecialchars($linkTitle) . '">' . htmlspecialchars((string)$val) . '</a>';
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

    <!-- Pagination & infos ("Affichage de X à Y sur Z"), toujours visibles sous le tableau -->
    <div id="<?php echo $paginationWrapperId; ?>" class="list-pagination-wrapper"></div>
</main>