<!-- Contenu principal — Détail d'une zone -->
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2>
            <span class="mif-earth mr-2"></span>
            <?php echo htmlspecialchars($data['zone'] ? $data['zone']->name : ($data['txt']['ZONE_TITLE_ZONES'] ?? 'Zone')); ?>
        </h2>
        <a href="<?php echo URLROOT; ?>/zone/zones" class="button secondary mt-2 mt-md-0">
            <span class="mif-arrow-left mr-1"></span>
            <?php echo $data['txt']['SYS_BTN_BACK'] ?? 'Retour'; ?>
        </a>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <?php if ($data['zone']): ?>
    <div class="card">
        <div class="card-content">
            <table class="table striped table-border">
                <tbody>
                    <tr>
                        <th><?php echo $data['txt']['ZONE_LABEL_NAME'] ?? 'Nom'; ?></th>
                        <td><?php echo htmlspecialchars($data['zone']->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $data['txt']['ZONE_LABEL_TYPE'] ?? 'Type'; ?></th>
                        <td><?php echo htmlspecialchars($data['zone']->type_code ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $data['txt']['ZONE_LABEL_COUNTRY_CODE'] ?? 'Code pays'; ?></th>
                        <td><?php echo htmlspecialchars($data['zone']->country_code ?? '—'); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $data['txt']['ZONE_LABEL_GEONAMES_ID'] ?? 'ID GeoNames'; ?></th>
                        <td>
                            <?php if ($data['zone']->geonames_id): ?>
                                <a href="https://www.geonames.org/<?php echo (int) $data['zone']->geonames_id; ?>"
                                   target="_blank" rel="noopener">
                                    <?php echo (int) $data['zone']->geonames_id; ?>
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $data['txt']['ZONE_LABEL_CHILDREN_LOADED'] ?? 'Sous-zones chargées'; ?></th>
                        <td>
                            <?php if ($data['zone']->children_loaded): ?>
                                <span class="badge success">Oui</span>
                            <?php else: ?>
                                <span class="badge secondary">Non</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
        <div class="remark warning">Zone introuvable.</div>
    <?php endif; ?>
</main>
