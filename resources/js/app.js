import tinymce from 'tinymce/tinymce';

import 'tinymce/icons/default';
import 'tinymce/models/dom';
import 'tinymce/themes/silver';
import 'tinymce/skins/ui/oxide/skin.min.css';
import 'tinymce/skins/content/default/content.min.css';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';
import 'tinymce/plugins/code';

const initializeTinyMCE = () => {
	const textarea = document.querySelector('#project-content');

	if (!textarea || textarea.dataset.tinymceInitialized === 'true') {
		return;
	}

	textarea.dataset.tinymceInitialized = 'true';

	tinymce.init({
		target: textarea,
		license_key: 'gpl',
		menubar: false,
		branding: false,
		promotion: false,
		skin: false,
		content_css: false,
		height: 360,
		plugins: 'lists link table code',
		toolbar:
			'undo redo | blocks | bold italic underline strikethrough | bullist numlist | link table | removeformat | code',
		block_formats:
			'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Blockquote=blockquote',
		content_style:
			'body { font-family: Inter, sans-serif; font-size: 14px; line-height: 1.6; }',
		setup(editor) {
			const form = textarea.closest('form');

			if (form) {
				form.addEventListener('submit', () => {
					editor.save();
				});
			}
		},
	});
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initializeTinyMCE);
} else {
	initializeTinyMCE();
}
