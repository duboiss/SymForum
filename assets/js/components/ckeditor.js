import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import '@ckeditor/ckeditor5-build-classic/build/translations/fr.js';

import '../../css/components/ckeditor.css';

ClassicEditor
    .create(document.querySelector('.editor'), {
        language: 'fr',
        mediaEmbed: {
            previewsInData: true
        },
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
    });
