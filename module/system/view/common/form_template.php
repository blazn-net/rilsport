<?php
/**
 * Template Parent Universel pour les Pages Form / View (RIL Sport)
 * 
 * Centralise :
 * - L'en-tête (titre selon mode, bouton Modifier pour admin en view, bouton Retour)
 * - Les alertes flash (message / error)
 * - Mode VIEW : card, avatar/icône, titre, code monospace, badge statut, divider, panneau d'audit déplié
 * - Mode EDIT/ADD : balise form, panneau d'audit replié en edit, boutons Enregistrer (success) / Annuler (neutre)
 * 
 * $config (ou $formConfig ou $data['formConfig']) attendu :
 * - mode            : 'view' | 'edit' | 'add' (défaut: $data['mode'] ?? 'view')
 * - item            : entité courante (objet ou tableau associatif)
 * - icon            : icône Metro UI (ex: 'mif-calendar')
 * - title           : titre générique ou par mode :
 *                     viewTitle, editTitle, addTitle
 * - editUrl         : URL vers le mode édition (ex: URLROOT . '/module/objet/edit/12')
 * - backUrl         : URL de retour (ex: URLROOT . '/module/objets')
 * - cancelUrl       : URL du bouton Annuler (défaut: viewUrl ou backUrl)
 * - formAction      : URL action du <form>
 * - idField         : nom de la clé id (défaut: 'id')
 * - codeField       : nom du champ code (défaut: 'code')
 * - nameField       : nom du champ nom (défaut: 'name')
 * - statusField     : nom du champ statut (défaut: 'status_id')
 * - viewAvatarHtml  : HTML optionnel pour l'avatar/logo de la fiche view
 * - viewAvatarIcon  : icône de repli de l'avatar (défaut: $config['icon'] ?? 'mif-file')
 * - viewContent     : callable ($item, $data) ou HTML du corps en consultation
 * - formContent     : callable ($item, $data, $mode) ou HTML des champs du formulaire
 * - showAudit       : booléen (défaut: true)
 * - auditData       : objet/tableau pour l'audit si différent de item
 */

$cfg = $formConfig ?? $data['formConfig'] ?? $config ?? [];

$mode      = $cfg['mode'] ?? $data['mode'] ?? 'view';
$item      = $cfg['item'] ?? null;
$row       = is_object($item) ? (array)$item : ($item ?? []);
$isAdmin   = !empty($data['isAdmin']);

// Clés d'identification
$idField     = $cfg['idField']     ?? 'id';
$codeField   = $cfg['codeField']   ?? 'code';
$nameField   = $cfg['nameField']   ?? 'name';
$statusField = $cfg['statusField'] ?? 'status_id';

$itemId     = $row[$idField]     ?? null;
$itemCode   = $row[$codeField]   ?? '';
$itemName   = $row[$nameField]   ?? '';
$itemStatus = isset($row[$statusField]) ? (int)$row[$statusField] : 1;

// Titres
if ($mode === 'edit') {
    $pageTitle = $cfg['editTitle'] ?? ($data['txt']['SYS_EDIT_TITLE'] ?? 'Modifier');
} elseif ($mode === 'add') {
    $pageTitle = $cfg['addTitle'] ?? ($data['txt']['SYS_ADD_TITLE'] ?? 'Ajouter');
} else {
    $pageTitle = $cfg['viewTitle'] ?? ('Fiche : ' . htmlspecialchars($itemName));
}

// Options d'affichage
$maxWidth    = $cfg['maxWidth']    ?? '960px';
$formEnctype = !empty($cfg['enctype']) ? ' enctype="' . htmlspecialchars($cfg['enctype']) . '"' : '';

// URLs
$backUrl   = $cfg['backUrl']   ?? '#';
$editUrl   = $cfg['editUrl']   ?? null;
$deleteUrl = $cfg['deleteUrl'] ?? null;
$deleteConfirm = $cfg['deleteConfirm'] ?? ($data['txt']['SYS_CONFIRM_DELETE'] ?? 'Êtes-vous sûr de vouloir supprimer cet élément ?');
$cancelUrl = $cfg['cancelUrl'] ?? ($mode === 'edit' && $itemId ? str_replace('/edit', '', $editUrl) : $backUrl);
$formAction = $cfg['formAction'] ?? '';

// Statut (view)
$isActive   = ($itemStatus === 1);
$badgeClass = $isActive ? 'success' : 'secondary';
$statusText = $isActive ? ($data['txt']['SPORT_ACTIVE'] ?? $data['txt']['SYS_STATUS_ACTIVE'] ?? 'Actif')
                        : ($data['txt']['SPORT_INACTIVE'] ?? $data['txt']['SYS_STATUS_INACTIVE'] ?? 'Inactif');

// Audit
$showAudit  = $cfg['showAudit'] ?? true;
$auditObj   = $cfg['auditData'] ?? $row;
$auditCreatedAt  = !empty($auditObj['created_at']) ? date('d/m/Y H:i', strtotime($auditObj['created_at'])) : '-';
$auditCreatedBy  = !empty($auditObj['created_by_name']) ? htmlspecialchars($auditObj['created_by_name']) : '-';
$auditModifiedAt = !empty($auditObj['modified_at']) ? date('d/m/Y H:i', strtotime($auditObj['modified_at'])) : '-';
$auditModifiedBy = !empty($auditObj['modified_by_name']) ? htmlspecialchars($auditObj['modified_by_name']) : '-';
?>
<main class="form-page p-4" style="padding-top: 68px; max-width: <?php echo htmlspecialchars($maxWidth); ?>; margin: 0 auto;">
    <!-- En-tête : Titre & Boutons d'action -->
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4" style="gap: 10px;">
        <h2 class="m-0">
            <?php if (!empty($cfg['icon'])): ?>
                <span class="<?php echo htmlspecialchars($cfg['icon']); ?> mr-2"></span>
            <?php endif; ?>
            <?php echo htmlspecialchars($pageTitle); ?>
        </h2>
        <div class="d-flex flex-align-center" style="gap: 10px;">
            <?php if ($mode === 'view' && $isAdmin && !empty($editUrl)): ?>
                <a href="<?php echo htmlspecialchars($editUrl); ?>"
                   class="button info"
                   id="btn-edit-main"
                   title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'); ?>">
                    <span class="mif-pencil"></span>
                    <span class="btn-text"><?php echo htmlspecialchars($data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'); ?></span>
                </a>
            <?php endif; ?>

            <?php if (!empty($backUrl)): ?>
                <a href="<?php echo htmlspecialchars($backUrl); ?>"
                   class="button"
                   id="btn-back-main"
                   title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_BACK'] ?? $data['txt']['USER_BTN_BACK'] ?? 'Retour'); ?>">
                    <span class="mif-arrow-left"></span>
                    <span class="btn-text"><?php echo htmlspecialchars($data['txt']['SYS_BTN_BACK'] ?? 'Retour'); ?></span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alertes Flash -->
    <?php if (!empty($data['message'])): ?>
        <div class="remark success mb-3"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert mb-3"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <?php if ($mode === 'view'): ?>
        <!-- ============================================================== -->
        <!-- MODE VIEW : Consultation pure (Zéro input, textes & badges)   -->
        <!-- ============================================================== -->
        <?php if (!empty($cfg['rawView']) && is_callable($cfg['rawView'])): ?>
            <?php call_user_func($cfg['rawView'], $item, $data); ?>
        <?php else: ?>
            <div class="card p-4">
                <!-- En-tête de la fiche : Avatar/Logo + Nom + Code + Badge statut -->
                <div class="d-flex flex-align-center flex-wrap mb-3" style="gap: 14px;">
                    <?php if (!empty($cfg['viewAvatarHtml'])): ?>
                        <?php echo $cfg['viewAvatarHtml']; ?>
                    <?php else: 
                        $avatarIcon = $cfg['viewAvatarIcon'] ?? $cfg['icon'] ?? 'mif-file';
                    ?>
                        <div style="width: 52px; height: 52px; min-width: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #0072c6; color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <span class="<?php echo htmlspecialchars($avatarIcon); ?> mif-2x"></span>
                        </div>
                    <?php endif; ?>

                    <div style="flex: 1; min-width: 200px;">
                        <h3 class="m-0" style="color: #1e293b;"><?php echo htmlspecialchars($itemName); ?></h3>
                        <?php if ($itemCode !== ''): ?>
                            <small class="fg-gray">Code : <code class="p-1 text-bold" style="background:#f1f5f9; color:#0284c7; border-radius:4px; font-size:12px;"><?php echo htmlspecialchars($itemCode); ?></code></small>
                        <?php endif; ?>
                    </div>

                    <div class="ml-auto">
                        <span class="badge <?php echo $badgeClass; ?> p-2 text-bold" style="font-size: 12px;">
                            <span class="<?php echo $isActive ? 'mif-checkmark' : 'mif-cross'; ?> mr-1"></span>
                            <?php echo htmlspecialchars($statusText); ?>
                        </span>
                    </div>
                </div>

                <div class="divider my-3"></div>

                <!-- Corps de consultation spécifique -->
                <div class="view-body mb-4">
                    <?php 
                    if (isset($cfg['viewContent'])) {
                        if (is_callable($cfg['viewContent'])) {
                            call_user_func($cfg['viewContent'], $item, $data);
                        } else {
                            echo $cfg['viewContent'];
                        }
                    }
                    ?>
                </div>

                <!-- Panneau d'audit (déplié par défaut en mode View) -->
                <?php if ($showAudit): ?>
                    <div data-role="panel"
                         data-title-caption="&lt;span class='mif-history mr-2'&gt;&lt;/span&gt;<?php echo htmlspecialchars($data['txt']['SYS_AUDIT_PANEL'] ?? $data['txt']['SPORT_INFO_PANEL'] ?? 'Informations d\'audit'); ?>"
                         data-collapsible="true"
                         data-collapsed="false"
                         class="mt-4">
                        <div class="row">
                            <div class="cell-md-6">
                                <p class="mb-2"><strong><span class="mif-calendar mr-1 fg-gray"></span><?php echo htmlspecialchars($data['txt']['SYS_CREATED_AT'] ?? $data['txt']['CREATED_AT'] ?? 'Créé le'); ?> :</strong> <?php echo $auditCreatedAt; ?></p>
                                <p class="mb-0"><strong><span class="mif-user mr-1 fg-gray"></span><?php echo htmlspecialchars($data['txt']['SYS_CREATED_BY'] ?? $data['txt']['CREATED_BY'] ?? 'Créé par'); ?> :</strong> <?php echo $auditCreatedBy; ?></p>
                            </div>
                            <div class="cell-md-6 mt-2 mt-md-0">
                                <p class="mb-2"><strong><span class="mif-pencil mr-1 fg-gray"></span><?php echo htmlspecialchars($data['txt']['SYS_MODIFIED_AT'] ?? $data['txt']['MODIFIED_AT'] ?? 'Modifié le'); ?> :</strong> <?php echo $auditModifiedAt; ?></p>
                                <p class="mb-0"><strong><span class="mif-user-check mr-1 fg-gray"></span><?php echo htmlspecialchars($data['txt']['SYS_MODIFIED_BY'] ?? $data['txt']['MODIFIED_BY'] ?? 'Modifié par'); ?> :</strong> <?php echo $auditModifiedBy; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- ============================================================== -->
        <!-- MODE EDIT / ADD : Formulaire interactif pour Administrateurs    -->
        <!-- ============================================================== -->
        <div class="card p-4">
            <form method="POST" action="<?php echo htmlspecialchars($formAction); ?>"<?php echo $formEnctype; ?> class="form-container">
                <!-- Corps du formulaire spécifique -->
                <div class="form-body">
                    <?php 
                    if (isset($cfg['formContent'])) {
                        if (is_callable($cfg['formContent'])) {
                            call_user_func($cfg['formContent'], $item, $data, $mode);
                        } else {
                            echo $cfg['formContent'];
                        }
                    }
                    ?>
                </div>

                <!-- Panneau d'audit (replié par défaut en mode Edit, masqué en Add) -->
                <?php if ($showAudit && $mode === 'edit'): ?>
                    <div data-role="panel"
                         data-title-caption="&lt;span class='mif-history mr-2'&gt;&lt;/span&gt;<?php echo htmlspecialchars($data['txt']['SYS_AUDIT_PANEL'] ?? $data['txt']['SPORT_INFO_PANEL'] ?? 'Informations d\'audit'); ?>"
                         data-collapsible="true"
                         data-collapsed="true"
                         class="mt-4">
                        <div class="row">
                            <div class="cell-md-6">
                                <p class="mb-2"><strong><span class="mif-calendar mr-1 fg-gray"></span><?php echo htmlspecialchars($data['txt']['SYS_CREATED_AT'] ?? $data['txt']['CREATED_AT'] ?? 'Créé le'); ?> :</strong> <?php echo $auditCreatedAt; ?></p>
                                <p class="mb-0"><strong><span class="mif-user mr-1 fg-gray"></span><?php echo htmlspecialchars($data['txt']['SYS_CREATED_BY'] ?? $data['txt']['CREATED_BY'] ?? 'Créé par'); ?> :</strong> <?php echo $auditCreatedBy; ?></p>
                            </div>
                            <div class="cell-md-6 mt-2 mt-md-0">
                                <p class="mb-2"><strong><span class="mif-pencil mr-1 fg-gray"></span><?php echo htmlspecialchars($data['txt']['SYS_MODIFIED_AT'] ?? $data['txt']['MODIFIED_AT'] ?? 'Modifié le'); ?> :</strong> <?php echo $auditModifiedAt; ?></p>
                                <p class="mb-0"><strong><span class="mif-user-check mr-1 fg-gray"></span><?php echo htmlspecialchars($data['txt']['SYS_MODIFIED_BY'] ?? $data['txt']['MODIFIED_BY'] ?? 'Modifié par'); ?> :</strong> <?php echo $auditModifiedBy; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Barre de boutons du formulaire -->
                <div class="form-group mt-4 d-flex flex-align-center flex-wrap" style="gap: 10px;">
                    <button class="button success" type="submit" id="btn-save-main">
                        <span class="mif-floppy-disk mr-1"></span>
                        <span class="btn-text">
                            <?php 
                            if ($mode === 'edit') {
                                echo htmlspecialchars($data['txt']['SYS_BTN_UPDATE'] ?? $data['txt']['USER_BTN_UPDATE'] ?? 'Mettre à jour');
                            } else {
                                echo htmlspecialchars($data['txt']['SYS_BTN_SAVE'] ?? $data['txt']['USER_BTN_SAVE'] ?? 'Enregistrer');
                            }
                            ?>
                        </span>
                    </button>

                    <?php if (!empty($cancelUrl)): ?>
                        <a href="<?php echo htmlspecialchars($cancelUrl); ?>" class="button secondary" id="btn-cancel-main">
                            <span class="mif-cancel mr-1"></span>
                            <span class="btn-text"><?php echo htmlspecialchars($data['txt']['SYS_BTN_CANCEL'] ?? 'Annuler'); ?></span>
                        </a>
                    <?php endif; ?>

                    <?php if ($mode === 'edit' && !empty($deleteUrl)): ?>
                        <a href="<?php echo htmlspecialchars($deleteUrl); ?>"
                           class="button alert ml-auto"
                           id="btn-delete-main"
                           onclick="return confirm('<?php echo addslashes($deleteConfirm); ?>');">
                            <span class="mif-bin mr-1"></span>
                            <span class="btn-text"><?php echo htmlspecialchars($data['txt']['SYS_BTN_DELETE'] ?? 'Supprimer'); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    <?php endif; ?>
</main>
