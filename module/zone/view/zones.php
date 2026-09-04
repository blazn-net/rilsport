<!-- Contenu principal — Treeview des zones géographiques -->
<style>
/* Style personnalisé pour le Treeview des zones */
.zone-tree-container {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 16px 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.zone-tree, .zone-tree ul {
    list-style: none !important;
    padding-left: 22px;
    margin: 0;
}
.zone-tree > ul {
    padding-left: 0;
}
.zone-node {
    list-style: none !important;
    margin: 3px 0;
    position: relative;
}
.zone-row {
    display: inline-flex;
    align-items: center;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    user-select: none;
    transition: background 0.15s ease;
    font-size: 14px;
    min-width: 440px;
}
.zone-row:hover {
    background-color: #f1f5f9;
}
.zone-toggle {
    display: inline-block;
    width: 18px;
    height: 18px;
    line-height: 18px;
    text-align: center;
    cursor: pointer;
    color: #64748b;
    margin-right: 6px;
    font-size: 12px;
    transition: transform 0.15s ease, color 0.15s ease;
}
.zone-toggle:hover {
    color: #1b6ec2;
}
.zone-toggle.mif-chevron-down {
    color: #1b6ec2;
}
.zone-icon {
    margin-right: 6px;
    font-size: 15px;
    vertical-align: middle;
}
.zone-label {
    font-weight: 500;
    color: #1e293b;
}
.zone-badge {
    font-size: 10px;
    padding: 2px 7px;
    margin-left: 8px;
    border-radius: 12px;
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.3px;
}
.zone-badge.badge-world     { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
.zone-badge.badge-continent { background: #fef3c7; color: #d97706; border-color: #fde68a; }
.zone-badge.badge-country   { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
.zone-badge.badge-admin1    { background: #f3e8ff; color: #7c3aed; border-color: #ddd6fe; }
.zone-badge.badge-admin2    { background: #f8fafc; color: #475569; border-color: #cbd5e1; }

.zone-actions {
    margin-left: auto;
    padding-left: 16px;
    display: inline-flex;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.15s ease;
}
.zone-row:hover .zone-actions {
    opacity: 1;
}
.zone-btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 4px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #64748b;
    font-size: 11px;
    text-decoration: none !important;
    transition: all 0.15s ease;
}
.zone-btn-action:hover {
    background: #e2e8f0;
    color: #0f172a;
    border-color: #94a3b8;
}
.zone-btn-action.action-add:hover {
    background: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
}
.zone-btn-action.action-delete:hover {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fca5a5;
}

.zone-children {
    border-left: 2px solid #e2e8f0;
    margin-left: 9px !important;
    padding-left: 14px !important;
}
.zone-loading {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 2px solid #cbd5e1;
    border-top-color: #1b6ec2;
    border-radius: 50%;
    animation: zone-spin 0.6s linear infinite;
    margin-left: 8px;
    vertical-align: middle;
}
@keyframes zone-spin {
    to { transform: rotate(360deg); }
}
</style>

<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2>
            <span class="mif-earth mr-2"></span>
            <?php echo $data['txt']['ZONE_TITLE_ZONES'] ?? 'Zones géographiques'; ?>
        </h2>
        <a href="<?php echo URLROOT; ?>/zone/add" class="button primary">
            <span class="mif-plus mr-1"></span>
            <?php echo $data['txt']['ZONE_TITLE_ZONE_ADD'] ?? 'Ajouter une zone'; ?>
        </a>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <!-- Barre de recherche -->
    <div class="input-control mb-4" style="max-width: 420px;">
        <input type="text" id="zone-search" placeholder="<?php echo htmlspecialchars($data['txt']['ZONE_PLACEHOLDER_SEARCH'] ?? 'Rechercher une zone...'); ?>" class="metro-input">
        <button class="button" type="button"><span class="mif-search"></span></button>
    </div>

    <!-- Treeview -->
    <div class="zone-tree-container">
        <div id="zone-tree" class="zone-tree">
            <?php if ($data['root']): ?>
                <ul>
                    <li id="zone-node-<?php echo (int) $data['root']['id']; ?>"
                        data-zone-id="<?php echo (int) $data['root']['id']; ?>"
                        data-type="world"
                        class="zone-node">
                        
                        <div class="zone-row" data-zone-id="<?php echo (int) $data['root']['id']; ?>">
                            <span class="zone-toggle mif-chevron-down" data-zone-id="<?php echo (int) $data['root']['id']; ?>"></span>
                            <span class="zone-icon mif-earth" style="color: #0284c7;"></span>
                            <span class="zone-label"><?php echo htmlspecialchars($data['root']['name'] ?? 'Monde'); ?></span>
                            <span class="zone-badge badge-world">Monde</span>
                            <span class="zone-loading" style="display:none;"></span>

                            <!-- Actions administrateur -->
                            <div class="zone-actions">
                                <a href="<?php echo URLROOT; ?>/zone/add?parent_id=<?php echo (int) $data['root']['id']; ?>"
                                   class="zone-btn-action action-add" title="Ajouter une sous-zone">
                                    <span class="mif-plus"></span>
                                </a>
                                <a href="<?php echo URLROOT; ?>/zone/<?php echo (int) $data['root']['id']; ?>"
                                   class="zone-btn-action" title="Modifier">
                                    <span class="mif-pencil"></span>
                                </a>
                            </div>
                        </div>

                        <!-- Continents pré-chargés immédiatement -->
                        <ul class="zone-children" data-parent-id="<?php echo (int) $data['root']['id']; ?>" style="display:block;">
                            <?php foreach ($data['continents'] as $continent): ?>
                                <li id="zone-node-<?php echo (int) $continent['id']; ?>"
                                    data-zone-id="<?php echo (int) $continent['id']; ?>"
                                    data-type="continent"
                                    class="zone-node">
                                    
                                    <div class="zone-row" data-zone-id="<?php echo (int) $continent['id']; ?>">
                                        <span class="zone-toggle mif-chevron-right" data-zone-id="<?php echo (int) $continent['id']; ?>"></span>
                                        <span class="zone-icon mif-location" style="color: #d97706;"></span>
                                        <span class="zone-label"><?php echo htmlspecialchars($continent['name']); ?></span>
                                        <span class="zone-badge badge-continent">Continent</span>
                                        <span class="zone-loading" style="display:none;"></span>

                                        <!-- Actions administrateur -->
                                        <div class="zone-actions">
                                            <a href="<?php echo URLROOT; ?>/zone/add?parent_id=<?php echo (int) $continent['id']; ?>"
                                               class="zone-btn-action action-add" title="Ajouter une sous-zone">
                                                <span class="mif-plus"></span>
                                            </a>
                                            <a href="<?php echo URLROOT; ?>/zone/<?php echo (int) $continent['id']; ?>"
                                               class="zone-btn-action" title="Modifier">
                                                <span class="mif-pencil"></span>
                                            </a>
                                            <a href="<?php echo URLROOT; ?>/zone/delete/<?php echo (int) $continent['id']; ?>"
                                               class="zone-btn-action action-delete"
                                               onclick="return confirm('Confirmer la suppression de « <?php echo addslashes($continent['name']); ?> » ?');"
                                               title="Supprimer">
                                                <span class="mif-bin"></span>
                                            </a>
                                        </div>
                                    </div>

                                    <ul class="zone-children" data-parent-id="<?php echo (int) $continent['id']; ?>" style="display:none;"></ul>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            <?php else: ?>
                <div class="remark warning">Aucune zone racine trouvée. Vérifiez que zone.sql a bien été exécuté.</div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
(function () {
    const URLROOT = '<?php echo URLROOT; ?>';

    // -------------------------------------------------------
    // Icônes et badges par type de zone
    // -------------------------------------------------------
    const typeConfig = {
        world:     { icon: 'mif-earth',    color: '#0284c7', label: 'Monde',       badge: 'badge-world' },
        continent: { icon: 'mif-location', color: '#d97706', label: 'Continent',   badge: 'badge-continent' },
        country:   { icon: 'mif-flag',     color: '#059669', label: 'Pays',        badge: 'badge-country' },
        admin1:    { icon: 'mif-map',      color: '#7c3aed', label: 'Région',      badge: 'badge-admin1' },
        admin2:    { icon: 'mif-pin',      color: '#475569', label: 'Département', badge: 'badge-admin2' }
    };

    // -------------------------------------------------------
    // Créer un nœud li pour une zone enfant
    // -------------------------------------------------------
    function createNode(zone) {
        const li = document.createElement('li');
        li.id           = 'zone-node-' + zone.id;
        li.dataset.zoneId = zone.id;
        li.dataset.type   = zone.type_code;
        li.className      = 'zone-node';

        const cfg     = typeConfig[zone.type_code] || { icon: 'mif-pin', color: '#666', label: zone.type_code, badge: 'badge-admin2' };
        const hasKids = (zone.type_code !== 'admin2');

        // Drapeau si pays avec country_code, sinon icône MetroUI
        let iconHtml = '';
        if (zone.type_code === 'country' && zone.country_code) {
            iconHtml = `<span class="fi fi-${zone.country_code.toLowerCase()} mr-2" style="font-size: 14px; border-radius: 2px;"></span>`;
        } else {
            iconHtml = `<span class="zone-icon ${cfg.icon}" style="color: ${cfg.color};"></span>`;
        }

        const safeName = escHtml(zone.name);

        li.innerHTML = `
            <div class="zone-row" data-zone-id="${zone.id}">
                <span class="zone-toggle ${hasKids ? 'mif-chevron-right' : 'mif-blank'}"
                      data-zone-id="${zone.id}"></span>
                ${iconHtml}
                <span class="zone-label">${safeName}</span>
                <span class="zone-badge ${cfg.badge}">${cfg.label}</span>
                <span class="zone-loading" style="display:none;"></span>

                <!-- Actions administrateur -->
                <div class="zone-actions">
                    ${hasKids ? `
                        <a href="${URLROOT}/zone/add?parent_id=${zone.id}"
                           class="zone-btn-action action-add" title="Ajouter une sous-zone">
                            <span class="mif-plus"></span>
                        </a>` : ''
                    }
                    <a href="${URLROOT}/zone/${zone.id}"
                       class="zone-btn-action" title="Modifier">
                        <span class="mif-pencil"></span>
                    </a>
                    <a href="${URLROOT}/zone/delete/${zone.id}"
                       class="zone-btn-action action-delete"
                       onclick="return confirm('Confirmer la suppression de cette zone ?');"
                       title="Supprimer">
                        <span class="mif-bin"></span>
                    </a>
                </div>
            </div>
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
        toggle.className = 'zone-toggle mif-loop';

        fetch(`${URLROOT}/zone/zone/children?parent_id=${zoneId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (spinner) spinner.style.display = 'none';

            if (data.error) {
                console.error('[Zone]', data.error);
                toggle.className = 'zone-toggle mif-warning text-danger';
                return;
            }

            childUl.innerHTML = '';
            if (data.zones && data.zones.length > 0) {
                data.zones.forEach(zone => childUl.appendChild(createNode(zone)));
                childUl.style.display = 'block';
                toggle.className = 'zone-toggle mif-chevron-down';
            } else {
                // Pas d'enfants (feuille)
                toggle.className = 'zone-toggle mif-blank';
            }

            attachToggleListeners(childUl);
        })
        .catch(err => {
            if (spinner) spinner.style.display = 'none';
            toggle.className = 'zone-toggle mif-warning text-danger';
            console.error('[Zone] Erreur AJAX', err);
        });
    }

    // -------------------------------------------------------
    // Action d'expansion / réduction d'un nœud
    // -------------------------------------------------------
    function toggleNode(zoneId) {
        const li = document.getElementById('zone-node-' + zoneId);
        if (!li) return;

        const toggle  = li.querySelector('.zone-toggle');
        const childUl = li.querySelector('.zone-children');
        if (!toggle || !childUl) return;

        // Si pas d'enfants encore insérés dans le DOM → appel AJAX
        if (childUl.children.length === 0) {
            loadChildren(zoneId, toggle, li);
        } else {
            // Basculer affichage
            if (childUl.style.display === 'none') {
                childUl.style.display = 'block';
                toggle.className = 'zone-toggle mif-chevron-down';
            } else {
                childUl.style.display = 'none';
                toggle.className = 'zone-toggle mif-chevron-right';
            }
        }
    }

    // -------------------------------------------------------
    // Attacher les listeners sur les lignes et chevrons
    // -------------------------------------------------------
    function attachToggleListeners(container) {
        container.querySelectorAll('.zone-row').forEach(row => {
            if (row.dataset.bound === 'true') return;
            row.dataset.bound = 'true';

            row.addEventListener('click', function (e) {
                // Ne pas déplier si on clique sur un bouton ou lien d'action
                if (e.target.closest('.zone-actions') || e.target.closest('a') || e.target.closest('button')) {
                    return;
                }
                const zoneId = this.dataset.zoneId;
                if (zoneId) {
                    toggleNode(zoneId);
                }
            });
        });
    }

    // -------------------------------------------------------
    // Recherche côté client
    // -------------------------------------------------------
    const searchInput = document.getElementById('zone-search');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            document.querySelectorAll('.zone-node').forEach(li => {
                const label = li.querySelector(':scope > .zone-row .zone-label');
                if (!label) return;
                if (!q) {
                    li.style.display = '';
                    return;
                }
                const match = label.textContent.toLowerCase().includes(q);
                li.style.display = match ? '' : 'none';
                if (match) {
                    // Déplier les ancêtres
                    let p = li.parentElement;
                    while (p && p.classList.contains('zone-children')) {
                        p.style.display = 'block';
                        const parentLi = p.closest('.zone-node');
                        if (parentLi) {
                            parentLi.style.display = '';
                            const t = parentLi.querySelector(':scope > .zone-row .zone-toggle');
                            if (t && t.classList.contains('mif-chevron-right')) {
                                t.className = 'zone-toggle mif-chevron-down';
                            }
                        }
                        p = parentLi ? parentLi.parentElement : null;
                    }
                }
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
    // Init : attacher les listeners sur les éléments déjà présents
    // -------------------------------------------------------
    const tree = document.getElementById('zone-tree');
    if (tree) {
        attachToggleListeners(tree);
    }
})();
</script>
