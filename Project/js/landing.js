// JavaScript for landing page functionality

// Helper function to find elements by text content
function findButtonByText(text) {
    const buttons = document.querySelectorAll('button');
    for (let i = 0; i < buttons.length; i++) {
        if (buttons[i].textContent.trim() === text) {
            return buttons[i];
        }
    }
    return null;
}

function findLinkByText(text) {
    const links = document.querySelectorAll('a');
    for (let i = 0; i < links.length; i++) {
        if (links[i].textContent.trim() === text) {
            return links[i];
        }
    }
    return null;
}

// Main function to set up all event listeners
function setupLandingPageEvents() {
    // Navigation links are handled by direct href attributes in the HTML
    // No JavaScript event listeners needed for these links

    // Top buttons
    const signUpBtn = findButtonByText('Sign Up');
    if (signUpBtn) {
        signUpBtn.addEventListener('click', function() {
            window.location.href = 'html/CreateAccount.html';
        });
    }

    const loginBtn = findButtonByText('Login');
    if (loginBtn) {
        loginBtn.addEventListener('click', function() {
            window.location.href = 'html/login.html';
        });
    }

    // Hero section buttons
    const becomeDonorBtn = findButtonByText('Become a Donor');
    if (becomeDonorBtn) {
        becomeDonorBtn.addEventListener('click', function() {
            window.location.href = 'html/CreateAccount.html';
        });
    }

    const findDonorsBtn = findButtonByText('Find Donors');
    if (findDonorsBtn) {
        findDonorsBtn.addEventListener('click', function() {
            window.location.href = 'html/Finder.html';
        });
    }

    // Role selection buttons
    const registerToDonateBtn = findButtonByText('Register to Donate');
    if (registerToDonateBtn) {
        registerToDonateBtn.addEventListener('click', function() {
            window.location.href = 'html/CreateAccount.html';
        });
    }

    const partnerWithUsBtn = findButtonByText('Partner With Us');
    if (partnerWithUsBtn) {
        partnerWithUsBtn.addEventListener('click', function() {
            alert('Hospital partnership functionality would go here');
        });
    }

    const searchForBloodBtn = findButtonByText('Search for Blood');
    if (searchForBloodBtn) {
        searchForBloodBtn.addEventListener('click', function() {
            window.location.href = 'html/Finder.html';
        });
    }

    // Footer links are handled by direct href attributes in the HTML
    // No JavaScript event listeners needed for these links

    // Footer buttons
    const readStoriesBtn = findButtonByText('Read More Stories');
    if (readStoriesBtn) {
        readStoriesBtn.addEventListener('click', function() {
            alert('Success stories would be displayed here');
        });
    }

    // Emergency hotline button
    // Alternative approach for emergency button
    const allFooterButtons = document.querySelectorAll('footer button');
    allFooterButtons.forEach(btn => {
        if (btn.textContent.includes('Emergency')) {
            btn.addEventListener('click', function() {
                alert('Emergency hotline would be called');
            });
        }
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', setupLandingPageEvents);