
document.addEventListener('DOMContentLoaded', function() {
    var abrirModal = document.querySelectorAll('.btn-delete');
    var modal = document.getElementById('modal');
    var cancelButton = modal.querySelector('.cancelar');
    var confirmButton = modal.querySelector('.confirmar');            

    abrirModal.forEach(function(btnDel) {
        btnDel.addEventListener('click', function () { 

            cancelButton.addEventListener('click', function() {
        
            modal.close();
            });

            confirmButton.addEventListener('click', function() {  
                
                var pathUserDeleteInput = document.getElementById(btnDel.id);                        
                var url = pathUserDeleteInput.value;
                console.log(url);
                window.location.href = url;
                modal.close();
            });                                     
        modal.showModal();
        });
    });            
});





