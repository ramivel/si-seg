// Example starter JavaScript for disabling form submissions if there are invalid fields
(() => {
  'use strict'

  const captcha =new Captcha($('#codigo_seguridad_imagen'), {
      width: 250,
      height: 50,
      length: 5,
      caseSensitive:true,
  });

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      var guardar = true;
      if(!form.checkValidity())
        guardar = false;
      
      if($('#codigo_seguridad').val()){
        if(!captcha.valid($('#codigo_seguridad').val())){
          Swal.fire({
            icon: "error",
            title: "El Código de Seguridad es Incorrecto",            
          });
          guardar = false;          
          captcha.refresh();
          $('#codigo_seguridad').val('');
        }
      }
      
      if (!guardar) {
        event.preventDefault();
        event.stopPropagation();
      }

      form.classList.add('was-validated')
    }, false)
  });



  $('#codigo_seguridad_refresh').on('click',function() { captcha.refresh(); });

})()
