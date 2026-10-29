<?php
/**
 * Template Maître de Page (Page Wrapper Universel) — RIL Sport
 * 
 * Centralise pour toutes les pages (List, Entity, Form, Dashboards...) :
 * 1. Le conteneur principal <main class="page-container ..." ...>
 * 2. L'en-tête de page standardisé :
 *    - Fil d'Ariane responsive Metro UI (ul.breadcrumbs) avec icône sur chaque nœud
 *    - Masquage automatique des libellés (.caption) sur mobile (≤ 576px / ≤ 414px)
 *    - Boutons d'action alignés à droite
 * 3. Les alertes flash universelles (remark success / remark alert)
 * 4. L'injection du contenu de la page ($pageConfig['content'])
 * 5. Point d'extension pour tout futur composant transverse (tabs, toolbar, etc.)
 */

$pg = $pageConfig ?? $data['pageConfig'] ?? [];

$pageClass = $pg['pageClass'] ?? 'p-4';
$maxWidth  = $pg['maxWidth'] ?? null;
$styleAttr = 'padding-top: 68px;';
if (!empty($maxWidth) && $maxWidth !== '100%' && $maxWidth !== 'none') {
    $styleAttr .= ' max-width: ' . htmlspecialchars($maxWidth) . '; margin: 0 auto;';
}

// ── Construction du Fil d'Ariane (Breadcrumbs Metro UI) ──────────────────────
if (!empty($pg['breadcrumbs']) && is_array($pg['breadcrumbs'])) {
    $breadcrumbs = $pg['breadcrumbs'];
} else {
    $breadcrumbs = [
        [
            'label' => $data['txt']['SYS_HOME'] ?? 'Accueil',
            'url'   => URLROOT . '/',
            'icon'  => 'mif-home',
        ]
    ];

    $moduleTitle = $pg['moduleTitle'] ?? null;
    $moduleUrl   = $pg['moduleUrl']   ?? null;
    $moduleIcon  = $pg['moduleIcon']  ?? null;
    $listUrl     = $pg['listUrl']     ?? ($pg['backUrl'] ?? null);

    // Détection automatique du module si non précisé
    if (empty($moduleTitle) && !empty($listUrl) && $listUrl !== '#') {
        $pathParts = explode('/', trim((string)parse_url($listUrl, PHP_URL_PATH), '/'));
        $rilIdx = array_search('rilsport', $pathParts);
        $modSlug = ($rilIdx !== false && isset($pathParts[$rilIdx + 1])) ? $pathParts[$rilIdx + 1] : ($pathParts[0] ?? '');
        if ($modSlug === 'sport') {
            $moduleTitle = $data['txt']['SPORT_SPORTS_MGT'] ?? 'Sports';
            $moduleUrl   = URLROOT . '/sport/sports';
            $moduleIcon  = 'mif-trophy';
        } elseif (in_array($modSlug, ['user', 'lang', 'zone'])) {
            $moduleTitle = $data['txt']['USER_MANAGEMENT'] ?? 'System';
            $moduleUrl   = URLROOT . '/user/users';
            $moduleIcon  = 'mif-cogs';
        }
    }

    if (!empty($moduleTitle)) {
        $breadcrumbs[] = [
            'label' => $moduleTitle,
            'url'   => $moduleUrl ?? '#',
            'icon'  => $moduleIcon ?? 'mif-folder',
        ];
    }

    $listTitle = $pg['listTitle'] ?? null;
    if (empty($listTitle) && !empty($listUrl) && $listUrl !== '#') {
        $pathParts = explode('/', trim((string)parse_url($listUrl, PHP_URL_PATH), '/'));
        $lastPart = end($pathParts);
        $listTitle = !empty($lastPart) ? ucfirst($lastPart) : 'Retour';
    }

    if (!empty($listTitle)) {
        $breadcrumbs[] = [
            'label' => $listTitle,
            'url'   => $listUrl ?? '#',
            'icon'  => $pg['listIcon'] ?? $pg['icon'] ?? 'mif-file-text',
        ];
    }

    // Sous-action courante optionnelle (ex: 'Modifier' en Edit, 'Ajouter' en Add)
    if (!empty($pg['actionTitle'])) {
        $breadcrumbs[] = [
            'label' => $pg['actionTitle'],
            'url'   => '#',
            'icon'  => $pg['actionIcon'] ?? null,
        ];
    }
}

$headerActionsHtml = $pg['headerActionsHtml'] ?? '';
?>
<main class="page-container <?php echo htmlspecialchars($pageClass); ?>" style="<?php echo $styleAttr; ?>">
    <!-- En-tête : Fil d'Ariane & Boutons d'action -->
    <div class="page-header d-flex flex-justify-between flex-align-center flex-wrap mb-4" style="gap: 10px;">
        <ul class="breadcrumbs m-0">
            <?php 
            $totalBc = count($breadcrumbs);
            foreach ($breadcrumbs as $idx => $bc): 
                $isLast = ($idx === $totalBc - 1);
            ?>
                <li class="<?php echo $isLast ? 'active' : ''; ?>">
                    <a href="<?php echo htmlspecialchars($bc['url'] ?? '#'); ?>" title="<?php echo htmlspecialchars($bc['label']); ?>">
                        <?php if (!empty($bc['icon'])): ?>
                            <span class="icon <?php echo htmlspecialchars($bc['icon']); ?>"></span>
                        <?php endif; ?>
                        <span class="caption"><?php echo htmlspecialchars($bc['label']); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if (!empty($headerActionsHtml)): ?>
            <div class="page-actions d-flex flex-align-center" style="gap: 10px;">
                <?php echo $headerActionsHtml; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Alertes Flash universelles -->
    <?php if (!empty($data['message'])): ?>
        <div class="remark success mb-3"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert mb-3"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <!-- Contenu spécifique de la page -->
    <?php 
    if (isset($pg['content'])) {
        if (is_callable($pg['content'])) {
            call_user_func($pg['content'], $data);
        } else {
            echo $pg['content'];
        }
    }
    ?>
</main>
