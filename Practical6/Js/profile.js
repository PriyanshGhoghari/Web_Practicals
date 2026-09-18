    const themeButton = document.getElementById("themeButton");
    const themeIcon = themeButton.querySelector("img");

    themeButton.addEventListener("click", function () {
        document.body.classList.toggle("dark-mode");

        if (document.body.classList.contains("da    rk-mode")) {
            themeIcon.src = "Images/sun.png";
        } else {
            themeIcon.src = "Images/moon.png";
        }
    });

    const profileForm = document.getElementById("profileForm");

    profileForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const studentId = document.getElementById("studentId").value.trim();
        const fullName = document.getElementById("fullName").value.trim();
        const dob = document.getElementById("dob").value.trim();
        const gender = document.getElementById("gender").value.trim();
        const phone = document.getElementById("phone").value.trim();
        const pincode = document.getElementById("pincode").value.trim();
        const aadhaar = document.getElementById("aadhaar").value.trim();
        const guardianName = document.getElementById("guardianName").value.trim();
        const guardianPhone = document.getElementById("guardianPhone").value.trim();

        const studentIdRegex = /^[A-Za-z0-9]{5,}$/;
        const nameRegex = /^[A-Za-z ]{3,}$/;
        const dateRegex = /^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/[0-9]{4}$/;
        const genderRegex = /^(Male|Female|Other)$/i;
        const mobileRegex = /^[6-9][0-9]{9}$/;
        const pincodeRegex = /^[0-9]{6}$/;
        const aadhaarRegex = /^[0-9]{12}$/;

        if (!studentIdRegex.test(studentId)) {
            alert("Enter a valid student ID");
            return;
        }

        if (!nameRegex.test(fullName)) {
            alert("Enter a valid full name");
            return;
        }

        if (!dateRegex.test(dob)) {
            alert("Enter date of birth in DD/MM/YYYY format");
            return;
        }

        if (!genderRegex.test(gender)) {
            alert("Enter Male, Female, or Other for gender");
            return;
        }

        if (!mobileRegex.test(phone)) {
            alert("Enter a valid phone number");
            return;
        }

        if (!pincodeRegex.test(pincode)) {
            alert("Enter a valid pincode");
            return;
        }

        if (!aadhaarRegex.test(aadhaar)) {
            alert("Enter a valid Aadhaar number");
            return;
        }

        if (!nameRegex.test(guardianName)) {
            alert("Enter a valid guardian name");
            return;
        }

        if (!mobileRegex.test(guardianPhone)) {
            alert("Enter a valid guardian phone number");
            return;
        }

        alert("Profile saved successfully");
    });
