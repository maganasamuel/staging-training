document.querySelectorAll('.form-check-input').forEach(function(input){
    input.addEventListener('change', function(e){
        console.log(e.target.value);
    })
});