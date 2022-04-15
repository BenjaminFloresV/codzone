document.addEventListener('DOMContentLoaded', () =>{
    console.log('works');
    let exitstMessage = !!document.getElementById('success-error-msg');
    if( exitstMessage ) {
        setTimeout(()=>{
            document.getElementById('success-error-msg').remove();
        }, 2000);
    }
})