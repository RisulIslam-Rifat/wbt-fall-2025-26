document.addEventListener('DOMContentLoaded', function () {
    const findDonorBtn = document.getElementById('findDonorBtn');
    const requestForm = document.getElementById('requestForm');
    const admittedHospitalSelect = document.getElementById('admittedHospitalSelect');
    const otherAdmittedInput = document.getElementById('otherAdmittedHospital');
    const sendRequestBtn = document.getElementById('sendRequestBtn');
    const requestsFeed = document.getElementById('requestsFeed');
    const emptyRequests = document.getElementById('emptyRequests');
    const totalRequestsCount = document.getElementById('totalRequestsCount');
    const userDisplayName = document.getElementById('userDisplayName');
    const profileName = document.getElementById('profileName');

    // Tabs & Search Elements
    const tabSearch = document.getElementById('tabSearch');
    const tabHistory = document.getElementById('tabHistory');
    const sectionSearch = document.getElementById('sectionSearch');
    const sectionHistory = document.getElementById('sectionHistory');
    const btnSearchDonors = document.getElementById('btnSearchDonors');
    const searchBloodGroup = document.getElementById('searchBloodGroup');
    const donorsList = document.getElementById('donorsList');

    // Tab Switching Logic
    function switchTab(tab) {
        if (tab === 'search') {
            tabSearch.classList.add('text-primary', 'border-primary');
            tabSearch.classList.remove('text-slate-500', 'border-transparent');
            tabHistory.classList.remove('text-primary', 'border-primary');
            tabHistory.classList.add('text-slate-500', 'border-transparent');

            sectionSearch.classList.remove('hidden');
            sectionHistory.classList.add('hidden');
        } else {
            tabHistory.classList.add('text-primary', 'border-primary');
            tabHistory.classList.remove('text-slate-500', 'border-transparent');
            tabSearch.classList.remove('text-primary', 'border-primary');
            tabSearch.classList.add('text-slate-500', 'border-transparent');

            sectionHistory.classList.remove('hidden');
            sectionSearch.classList.add('hidden');
        }
    }

    tabSearch.addEventListener('click', () => switchTab('search'));
    tabHistory.addEventListener('click', () => switchTab('history'));

    // Search Donors Functionality
    btnSearchDonors.addEventListener('click', () => {
        const bg = searchBloodGroup.value;
        const url = bg ? `../php/get_donors.php?blood_group=${encodeURIComponent(bg)}` : `../php/get_donors.php`;

        btnSearchDonors.innerText = 'Searching...';
        btnSearchDonors.disabled = true;
        donorsList.innerHTML = '<div class="text-center py-10"><span class="material-symbols-outlined animate-spin">progress_activity</span></div>';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                btnSearchDonors.innerText = 'Search Donors';
                btnSearchDonors.disabled = false;

                if (data.success) {
                    renderDonors(data.donors);
                } else {
                    donorsList.innerHTML = `<div class="text-center text-red-500 py-4">Error: ${data.message}</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                btnSearchDonors.innerText = 'Search Donors';
                btnSearchDonors.disabled = false;
                donorsList.innerHTML = `<div class="text-center text-red-500 py-4">Failed to fetch donors.</div>`;
            });
    });

    function renderDonors(donors) {
        if (!donors || donors.length === 0) {
            donorsList.innerHTML = `<div class="text-center py-10 text-slate-500">No donors found matching your criteria.</div>`;
            return;
        }

        donorsList.innerHTML = '';
        donors.forEach(donor => {
            const card = document.createElement('div');
            card.className = "bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 flex flex-col md:flex-row items-center gap-4 shadow-sm hover:shadow-md transition-all";
            card.innerHTML = `
                <div class="size-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-lg">
                    ${(donor.blood_group === 'Unknown' || !donor.blood_group) ? '?' : donor.blood_group}
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h4 class="font-bold text-[#0d141b] dark:text-white">${donor.fullname}</h4>
                    <p class="text-sm text-slate-500">${donor.hospital || 'No Hospital'}</p>
                </div>
                <div class="flex gap-2">
                    <a href="tel:${donor.phone}" class="h-9 px-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-700 text-sm font-bold flex items-center gap-2 hover:bg-slate-100 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                        <span class="material-symbols-outlined text-[16px]">call</span> Call
                    </a>
                </div>
            `;
            donorsList.appendChild(card);
        });
    }

    // Load User Data
    const currentUser = JSON.parse(localStorage.getItem('user') || localStorage.getItem('currentUser') || '{}');
    const displayName = currentUser.name || currentUser.fullname || 'User';

    if (displayName) {
        userDisplayName.textContent = displayName;
        profileName.textContent = displayName;
    }

    // Show form on "New Request" click
    findDonorBtn.addEventListener('click', () => {
        requestForm.style.display = 'block';
        findDonorBtn.style.display = 'none';
        requestForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // Load and render existing requests from Database
    function loadRequests() {
        fetch('../php/blood_requests.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderRequests(data.requests);
                } else {
                    console.error('Failed to load requests');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function renderRequests(requests) {
        let myRequests = [];
        const myUserId = currentUser.id ? parseInt(currentUser.id) : null;

        if (myUserId) {
            myRequests = requests.filter(req => req.user_id == myUserId);
        } else {
            // Fallback to email if ID missing (legacy)
            myRequests = requests.filter(req => req.email === currentUser.email);
        }

        totalRequestsCount.textContent = myRequests.length;

        if (myRequests.length === 0) {
            emptyRequests.style.display = 'flex';
            // Clear feed but keep empty message
            const cards = requestsFeed.querySelectorAll('.request-card');
            cards.forEach(c => c.remove());
            return;
        }

        emptyRequests.style.display = 'none';

        // Clear previous cards
        const existingCards = requestsFeed.querySelectorAll('.request-card');
        existingCards.forEach(c => c.remove());

        myRequests.forEach((req, index) => {
            const card = document.createElement('div');
            card.className = 'request-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row gap-6 relative overflow-hidden group transition-all hover:shadow-md';

            const timeAgo = Math.floor((new Date() - new Date(req.timestamp)) / 60000); // in minutes
            let timeStr = timeAgo < 1 ? 'Just now' : `${timeAgo}m ago`;
            if (timeAgo > 60) timeStr = `${Math.floor(timeAgo / 60)}h ago`;
            if (timeAgo > 1440) timeStr = `${Math.floor(timeAgo / 1440)}d ago`;

            card.innerHTML = `
                <div class="size-16 shrink-0 rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-600 flex flex-col items-center justify-center">
                    <span class="text-[10px] font-bold uppercase">Type</span>
                    <span class="text-2xl font-black">${req.bloodGroup}</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 font-bold text-[10px] uppercase">Pending</span>
                        <span class="text-[11px] text-slate-400">Submitted ${timeStr}</span>
                    </div>
                    <h4 class="font-bold text-lg mb-1">${req.admittedHospital}</h4>
                    <p class="text-sm text-slate-500">${req.patientLocation} • Contact: ${req.contacts[0]}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="deleteRequest(${req.id})" class="size-10 rounded-lg border border-slate-200 dark:border-slate-800 flex items-center justify-center text-slate-400 hover:text-red-500 hover:border-red-100 transition-all">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                    <button class="h-10 px-4 bg-primary text-white text-xs font-bold rounded-lg hover:bg-blue-600 transition-all">
                        Track Status
                    </button>
                </div>
            `;
            requestsFeed.appendChild(card);
        });
    }

    // Delete Request
    window.deleteRequest = function (id) {
        if (confirm('Are you sure you want to delete this request?')) {
            fetch('../php/blood_requests.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadRequests(); // Reload
                    } else {
                        alert('Failed to delete: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    };

    // Handle Send Request
    sendRequestBtn.addEventListener('click', () => {
        // Collect Data
        const bloodGroup = document.getElementById('bloodGroup').value;
        let admittedHospital = admittedHospitalSelect.value;
        if (admittedHospital === 'Other') {
            admittedHospital = otherAdmittedInput.value;
        }
        const requestHospitals = Array.from(document.querySelectorAll('input[name="requestHospitals"]:checked')).map(cb => cb.value);
        const patientLocation = document.getElementById('patientLocation').value;
        const contact1 = document.getElementById('contact1').value;
        const contact2 = document.getElementById('contact2').value;

        // Validation
        if (!bloodGroup) {
            alert('Please select a blood group.');
            return;
        }
        if (!admittedHospital) {
            alert('Please select or enter the admitted hospital.');
            return;
        }
        if (!patientLocation) {
            alert('Please enter the patient location.');
            return;
        }
        if (!contact1) {
            alert('Please enter primary contact number.');
            return;
        }

        const newRequest = {
            bloodGroup,
            admittedHospital,
            requestHospitals,
            patientLocation,
            contacts: [contact1, contact2].filter(c => c),
            requesterEmail: currentUser.email,
            timestamp: new Date().toISOString() // Kept for consistency, but backend uses auto-timestamp
        };

        // Send to Backend
        fetch('../php/blood_requests.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(newRequest)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Blood request broadcasted successfully!');
                    document.getElementById('submissionForm').reset();
                    requestForm.style.display = 'none';
                    findDonorBtn.style.display = 'block';
                    if (otherAdmittedInput) otherAdmittedInput.style.display = 'none';
                    loadRequests(); // Reload
                } else {
                    alert('Failed to submit request: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred: ' + error.message + '. Please check console for details.');
            });
    });

    // Helper for "Other" hospital
    window.toggleOtherHospital = function (value) {
        if (value === 'Other') {
            otherAdmittedInput.style.display = 'block';
            otherAdmittedInput.required = true;
        } else {
            otherAdmittedInput.style.display = 'none';
            otherAdmittedInput.required = false;
        }
    };

    // Initial Load
    loadRequests();
});