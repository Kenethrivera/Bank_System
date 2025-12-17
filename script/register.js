const registerForm = document.getElementById('registerForm');
    const errorMessage = document.getElementById('errorMessage');
    const profileInput = document.getElementById('profilePicture');
    const profileWrapper = document.getElementById('profileWrapper');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const profileLabel = document.getElementById('profileLabel');

    let profilePictureData = null;

    profileInput.addEventListener('change', function(e){
      const file = e.target.files[0];
      if(file){
        if(file.size > 5*1024*1024){
          alert('Image size must be less than 5MB');
          profileInput.value = '';
          return;
        }
        if(!file.type.startsWith('image/')){
          alert('Please upload an image file');
          profileInput.value = '';
          return;
        }
        const reader = new FileReader();
        reader.onload = function(){
          profilePictureData = reader.result;
          profileWrapper.innerHTML = `
            <img src="${profilePictureData}" alt="Profile">
            <button type="button" id="removeProfileBtn" class="remove-picture-btn">&times;</button>
          `;
          document.getElementById('removeProfileBtn').addEventListener('click', function(){
            profilePictureData = null;
            profileWrapper.innerHTML = '';
            profileWrapper.appendChild(uploadPlaceholder);
            profileInput.value = '';
            profileLabel.textContent = 'Upload Profile Picture';
          });
          profileLabel.textContent = 'Change Photo';
        };
        reader.readAsDataURL(file);
      }
    });

    registerForm.addEventListener('submit', function(e){
      e.preventDefault();
      errorMessage.classList.add('d-none');

      const formData = Object.fromEntries(new FormData(registerForm).entries());
      const fullName = [formData.firstName, formData.middleName, formData.lastName].filter(Boolean).join(' ');

      if(!formData.firstName || !formData.lastName){
        errorMessage.textContent = 'First name and last name are required';
        errorMessage.classList.remove('d-none');
        return;
      }
      if(formData.password !== formData.confirmPassword){
        errorMessage.textContent = 'Passwords do not match';
        errorMessage.classList.remove('d-none');
        return;
      }
      if(formData.password.length < 6){
        errorMessage.textContent = 'Password must be at least 6 characters';
        errorMessage.classList.remove('d-none');
        return;
      }

      alert('Account created successfully!\nFull Name: '+fullName);
    });