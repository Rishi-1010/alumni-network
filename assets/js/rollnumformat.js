document.addEventListener('DOMContentLoaded', function () {
    console.log('rollnumformat.js loaded'); // Debugging line

    const searchForm = document.querySelector('.search-form');
    if (!searchForm) {
        console.error('Search form not found');
        return;
    }

    const enrollmentInput = searchForm.querySelector('input[name="search_enrollment"]');
    if (!enrollmentInput) {
        console.error('Enrollment input not found');
        return;
    }

    const errorMessage = document.createElement('div');
    errorMessage.style.color = 'red';
    errorMessage.style.marginTop = '10px';

    searchForm.appendChild(errorMessage);

    searchForm.addEventListener('submit', function (event) {
        console.log('Form submitted'); // Debugging line
        const enrollmentNumber = enrollmentInput.value.trim();
        errorMessage.textContent = ''; // Clear previous error messages

        if (!/^\d{15}$/.test(enrollmentNumber)) {
            event.preventDefault();
            errorMessage.textContent = 'Invalid enrollment number format. It must be a 15-digit number.';
            console.log('Invalid format'); // Debugging line
            return;
        }

        const enrolledYear = enrollmentNumber.substring(0, 4);
        const courseCode = enrollmentNumber.substring(7, 11);
        const uniqueNumber = enrollmentNumber.substring(12, 15);

        const courseDetails = getCourseDetails(courseCode);

        if (!courseDetails) {
            event.preventDefault();
            errorMessage.textContent = 'Invalid course code in enrollment number.';
            console.log('Invalid course code'); // Debugging line
            return;
        }

        // Optionally, display parsed information
        console.log(`Enrolled Year: ${enrolledYear}`);
        console.log(`Course: ${courseDetails.course}`);
        console.log(`Department: ${courseDetails.department}`);
    });

    function getCourseDetails(courseCode) {
        const courses = {
            '0051': { course: 'BCA', department: 'BVPICS' },
            // Add more courses as needed
        };
        return courses[courseCode] || null;
    }
}); 