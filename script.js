
$(document).ready(function() {
    // Functionality for adding new skill rows
    $('#addSkillBtn').click(function() {
        var newIndex = $('#skillList .skillRow').length;
        var newRow = $('.skillRow:first').clone();
        newRow.attr('data-skill-index', newIndex);
        newRow.find('input[type="text"]').val('');
        newRow.find('input[type="radio"]').prop('checked', false);
        $('#skillList').append(newRow);
    });

    // Functionality for saving skills to localStorage (example)
    $('#saveBtn').click(function() {
        // Logic to gather skill data and save it
        alert('Skills saved (example logic)');
    });

    // Functionality for generating PDF
    $('#createPdfBtn').click(function() {
        // Logic to generate PDF from skill list (requires implementation)
        alert('Generate PDF (requires implementation)');
    });

    // Toggle Dark/Light Mode
    $('#toggleDarkMode').click(function() {
        $('body').toggleClass('dark-mode');
    });
});
