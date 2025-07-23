// Function to convert date to words
function convertDateToWords(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    const day = date.getDate();
    const month = date.toLocaleString('default', { month: 'long' });
    const year = date.getFullYear();
    
    const dayWords = convertNumberToWords(day);
    const yearWords = convertNumberToWords(year);
    
    return `${dayWords} ${month} ${yearWords}`;
}

function convertNumberToWords(num) {
    const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    
    if (num === 0) return 'Zero';
    if (num < 20) return ones[num];
    if (num < 100) return tens[Math.floor(num / 10)] + (num % 10 ? ' ' + ones[num % 10] : '');
    if (num < 1000) return ones[Math.floor(num / 100)] + ' Hundred' + (num % 100 ? ' ' + convertNumberToWords(num % 100) : '');
    
    return num.toString();
}

// Auto-fill date of birth in words when date is selected
function setupDateAutoFill() {
    const dateInput = document.getElementById('date_of_birth');
    const wordsInput = document.getElementById('date_of_birth_words');
    
    if (dateInput && wordsInput) {
        dateInput.addEventListener('change', function() {
            wordsInput.value = convertDateToWords(this.value);
            M.updateTextFields();
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    setupDateAutoFill();
});
