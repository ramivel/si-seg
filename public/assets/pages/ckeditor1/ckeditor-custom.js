"use strict";    
/*inline ckeditor*/
/*InlineEditor
		.create( document.querySelector( '#editor' ), {
			// toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
		} )
		.then( editor => {
			window.editor = editor;
		} )
		.catch( err => {
			console.error( err.stack );
		} );*/
ClassicEditor.create(document.querySelector( '#ckeditor' ), {        
    language: 'es'
}).then( 
    editor => {
		window.editor = editor;
	}
).catch( 
    err => {
		console.error( err.stack );
		} 
);