<?php
/**
 * Qadamchi component: button
 * Parametrlar:
 *   - type: button | submit | reset (default: button)
 *   - color: primary | secondary | danger | success | ... (default: primary)
 *   - class: qo'shimcha css klasslar
 *   - href: agar mavjud bo'lsa, <a> shaklida bo'ladi
 *   - disabled: true/false
 *   - text: tugma matni (yoki $slot)
 */
$type = $type ?? 'button';
$color = $color ?? 'primary';
$class = $class ?? '';
$disabled = !empty($disabled) ? 'disabled' : '';
$tag = isset($href) ? 'a' : 'button';
$text = $text ?? ($slot ?? '');
?>
<<?= $tag ?>
    <?= isset($href) ? 'href="'.htmlspecialchars($href).'"' : 'type="'.htmlspecialchars($type).'"' ?>
    class="btn btn-<?= htmlspecialchars($color) ?> <?= $class ?>" <?= $disabled ?>>
    <?= $text ?>
</<?= $tag ?>>