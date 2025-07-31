<?php

namespace FlexibleShippingUspsVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var \WPDesk\View\Renderer\Renderer $renderer
 * @var string $name_prefix
 * @var string $value
 * @var string $template_name Real field template.
 */
?>

<style>
	input[type="checkbox"].wpd-toggle-field {
		all:unset;
		background: #fff;
		border:1px solid #949494;
		width:32px;
		height: 16px;
		position: relative;
		cursor: pointer;
		border-radius: 200px;
	}
	input[type="checkbox"].wpd-toggle-field::before {
		all:unset;
		content: "";
		display: block !important;
		height: 14px;
		width:14px;
		background: #1c1c1c;
		border-radius: 100%;
		position: absolute;
		left: 1px;
		top:1px;
		transition: 0.25s;
		transition-timing-function: ease-out;
	}
	input[type="checkbox"].wpd-toggle-field:checked {
		background: var(--wp-admin-theme-color, #1851e0);
		border-color: var(--wp-admin-theme-color, #1851e0);
	}
	input[type="checkbox"].wpd-toggle-field:checked:before {
		left: 17px;
		background: #fff;
	}

</style>
<?php 
$renderer->output_render('input', ['field' => $field, 'renderer' => $renderer, 'name_prefix' => $name_prefix, 'value' => $value]);
