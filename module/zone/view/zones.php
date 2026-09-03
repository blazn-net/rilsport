<!-- Contenu principal — Treeview des zones géographiques -->
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2><span class="mif-earth mr-2"></span><?php echo $data['txt']['ZONE_TITLE_ZONES'] ?? 'Zones géographiques'; ?></h2>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <!-- Barre de recherche -->
    <div class="input-control mb-4" style="max-width: 400px;">
        <input type="text" id="zone-search" placeholder="<?php echo htmlspecialchars($data['txt']['ZONE_PLACEHOLDER_SEARCH'] ?? 'Rechercher...'); ?>" class="metro-input">
        <button class="button"><span class="mif-search"></span></button>
    </div>

    <!-- Treeview -->
    <div id="zone-tree" class="tree-view">
        <?php if ($data['root']): ?>
            <ul>
                <li id="zone-node-<?php echo (int) $data['root']['id']; ?>"
                    data-zone-id="<?php echo (int) $data['root']['id']; ?>"
                    data-loaded="<?php echo $data['root']['children_loaded'] ? 'true' : 'false'; ?>"
                    class="zone-node">
                    <span class="zone-toggle mif-chevron-right" data-zone-id="<?php echo (int) $data['root']['id']; ?>"></span>
                    <span class="mif-earth mr-1"></span>
                    <span class="zone-label"><?php echo htmlspecialchars($data['root']['name'] ?? 'Monde'); ?></span>
                    <span class="zone-loading spinner ml-2" style="display:none;"></span>
                    <ul class="zone-children" data-parent-id="<?php echo (int) $data['root']['id']; ?>" style="display:none;"></ul>
                </li>
            </ul>
        <?php else: ?>
            <div class="remark warning">Aucune zone racine trouvée. Vérifiez que zone.sql a bien été exécuté.</div>
        <?php endif; ?>
    </div>
</main>

<script>
(function () {
    const URLROOT = '<?php echo URLROOT; ?>';

    // -------------------------------------------------------
    // Icônes par type de zone
    // -------------------------------------------------------
    const typeIcons = {
        world:     'mif-earth',
        continent: 'mif-location',
        country:   'mif-flag',
        admin1:    'mif-map',
        admin2:    'mif-pin'
    };

    // -------------------------------------------------------
    // Créer un nœud li pour une zone
    // -------------------------------------------------------
    function createNode(zone) {
        const li = document.createElement('li');
        li.id            = 'zone-node-' + zone.id;
        li.dataset.zoneId  = zone.id;
        li.dataset.loaded  = 'false';
        li.className       = 'zone-node';

        const icon    = typeIcons[zone.type_code] || 'mif-pin';
        const hasKids = (zone.type_code !== 'admin2'); // admin2 = feuille

        li.innerHTML = `
            <span class="zone-toggle ${hasKids ? 'mif-chevron-right' : 'mif-blank'}"
                  data-zone-id="${zone.id}"></span>
            <span class="${icon} mr-1"></span>
            <span class="zone-label">${escHtml(zone.name)}</span>
            <span class="zone-loading spinner ml-2" style="display:none;"></span>
            ${hasKids ? `<ul class="zone-children" data-parent-id="${zone.id}" style="display:none;"></ul>` : ''}
        `;
        return li;
    }

    // -------------------------------------------------------
    // Charger les enfants d'une zone via AJAX
    // -------------------------------------------------------
    function loadChildren(zoneId, toggle, li) {
        const spinner = li.querySelector('.zone-loading');
        const childUl = li.querySelector('.zone-children');
        if (!childUl) return;

        if (spinner) spinner.style.display = 'inline-block';
        toggle.className = toggle.className.replace('mif-chevron-right', 'mif-loop');

        fetch(`${URLROOT}/zone/zone/children?parent_id=${zoneId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (spinner) spinner.style.display = 'none';

            if (data.error) {
                console.error('[Zone]', data.error);
                toggle.className = toggle.className.replace('mif-loop', 'mif-warning');
                return;
            }

            childUl.innerHTML = '';
            if (data.zones && data.zones.length > 0) {
                data.zones.forEach(zone => childUl.appendChild(createNode(zone)));
                childUl.style.display = 'block';
                toggle.className = toggle.className.replace('mif-loop', 'mif-chevron-down');
            } else {
                // Pas d'enfants
                toggle.className = toggle.className.replace('mif-loop', 'mif-blank');
            }

            li.dataset.loaded = 'true';
            attachToggleListeners(childUl);
        })
        .catch(err => {
            if (spinner) spinner.style.display = 'none';
            toggle.className = toggle.className.replace('mif-loop', 'mif-warning');
            console.error('[Zone] Erreur AJAX', err);
        });
    }

    // -------------------------------------------------------
    // Attacher les listeners de toggle sur les nœuds
    // -------------------------------------------------------
    function attachToggleListeners(container) {
        container.querySelectorAll('.zone-toggle').forEach(toggle => {
            // Éviter les doublons
            if (toggle.dataset.bound === 'true') return;
            toggle.dataset.bound = 'true';

            toggle.addEventListener('click', function () {
                const zoneId = this.dataset.zoneId;
                const li     = document.getElementById('zone-node-' + zoneId);
                const childUl = li ? li.querySelector('.zone-children') : null;

                if (!li || !childUl) return;

                if (li.dataset.loaded !== 'true') {
                    // Premier clic : charger depuis GeoNames / BDD
                    loadChildren(zoneId, this, li);
                } else {
                    // Toggle visibility
                    if (childUl.style.display === 'none') {
                        childUl.style.display = 'block';
                        this.className = this.className.replace('mif-chevron-right', 'mif-chevron-down');
                    } else {
                        childUl.style.display = 'none';
                        this.className = this.className.replace('mif-chevron-down', 'mif-chevron-right');
                    }
                }
            });
        });
    }

    // -------------------------------------------------------
    // Recherche côté client (filtre les labels visibles)
    // -------------------------------------------------------
    const searchInput = document.getElementById('zone-search');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            document.querySelectorAll('.zone-label').forEach(label => {
                const li = label.closest('li.zone-node');
                if (!li) return;
                if (!q) {
                    li.style.display = '';
                    return;
                }
                li.style.display = label.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    // -------------------------------------------------------
    // Utilitaire escHtml
    // -------------------------------------------------------
    function escHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str || ''));
        return d.innerHTML;
    }

    // -------------------------------------------------------
    // Init : attacher les listeners sur la racine
    // -------------------------------------------------------
    const tree = document.getElementById('zone-tree');
    if (tree) attachToggleListeners(tree);

})();
</script>
