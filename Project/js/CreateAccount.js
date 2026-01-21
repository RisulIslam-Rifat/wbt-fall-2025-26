document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('roleSelect');
    const formContainer = document.getElementById('formContainer');
    const dynamicFields = document.getElementById('dynamicFields');
    const form = document.getElementById('accountForm');

    formContainer.style.display = 'none';

    roleSelect.addEventListener('change', function () {
        const role = roleSelect.value;
        formContainer.style.display = role ? 'block' : 'none';
        dynamicFields.innerHTML = '';

        let html = '';

        // Hospital required for Donor only
        if (role === 'donor') {
            html += `
                <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-xl">
                    <h3 class="text-lg font-bold mb-6 pb-3 border-b border-slate-200 dark:border-slate-700">Donor Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <label class="flex flex-col gap-2">
                            <span class="text-sm font-medium">Associated Hospital or Blood Bank</span>
                            <input type="text" id="hospital" name="hospital" placeholder="e.g., City General Hospital" 
                                   class="h-12 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-primary focus:border-primary" 
                                   required />
                        </label>
                        <label class="flex flex-col gap-2">
                            <span class="text-sm font-medium">Blood Group</span>
                            <select id="bloodGroup" name="bloodGroup" required
                                class="h-12 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-primary focus:border-primary">
                                <option value="">-- Select --</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </label>
                    </div>
                </div>
            `;
        }

        if (role === 'donor') {
            html += `
                <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-xl">
                    <h3 class="text-lg font-bold mb-4 pb-3 border-b border-slate-200 dark:border-slate-700">Health & Eligibility Information</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 italic mb-6"><em>All donor health information is confidential and used only for blood safety.</em></p>

                    <div class="flex flex-col gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-3">Do you currently smoke or use tobacco?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="smoking" value="yes" class="text-primary focus:ring-primary" required />
                                    <span class="text-sm">Yes</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="smoking" value="no" class="text-primary focus:ring-primary" required />
                                    <span class="text-sm">No</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-3">Do you have any chronic medical conditions? (e.g., diabetes, HIV, hepatitis)</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="chronicIllness" value="yes" class="text-primary focus:ring-primary"  required />
                                    <span class="text-sm">Yes</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="chronicIllness" value="no" class="text-primary focus:ring-primary" required />
                                    <span class="text-sm">No</span>
                                </label>
                            </div>
                        </div>

                        <div id="illnessDetails" style="display:none;" class="ml-4">
                            <label class="flex flex-col gap-2">
                                <span class="text-sm font-medium">Please specify condition(s)</span>
                                <textarea id="illnessDetailsText" name="illnessDetails" rows="2" placeholder="e.g., Epilepsy, Asthma, etc."
                                          class="rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-primary focus:border-primary"></textarea>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-3">Have you had a tattoo or piercing in the last 6 months?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="tattoo" value="yes" class="text-primary focus:ring-primary" required />
                                    <span class="text-sm">Yes</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="tattoo" value="no" class="text-primary focus:ring-primary" required />
                                    <span class="text-sm">No</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-3">Have you traveled to a malaria-risk area in the past year?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="travel" value="yes" class="text-primary focus:ring-primary" required />
                                    <span class="text-sm">Yes</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="travel" value="no" class="text-primary focus:ring-primary" required />
                                    <span class="text-sm">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div >
                `;

            // Re-attach event after DOM update (using timeout to ensure elements exist)
            setTimeout(() => {
                const illnessYes = document.querySelector('input[name="chronicIllness"][value="yes"]');
                const illnessNo = document.querySelector('input[name="chronicIllness"][value="no"]');
                const detailsDiv = document.getElementById('illnessDetails');

                if (illnessYes && illnessNo && detailsDiv) {
                    const toggleDetails = () => {
                        detailsDiv.style.display = illnessYes.checked ? 'block' : 'none';
                    };
                    illnessYes.addEventListener('change', toggleDetails);
                    illnessNo.addEventListener('change', toggleDetails);
                }
            }, 50);
        }
        else if (role === 'finder') {
            html += `<p class="text-sm text-slate-600 dark:text-slate-400 italic px-6"><em>You can search for blood donors after your account is approved.</em></p>`;
        }

        dynamicFields.innerHTML = html;
    });

    // Handle form submission
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const role = roleSelect.value;
            if (!role) {
                alert('Please select a role.');
                return;
            }

            const registrationData = {
                fullname: document.getElementById('fullname').value.trim(),
                email: document.getElementById('email').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                password: document.getElementById('password').value,
                role: role
            };

            // Add hospital if applicable
            if (role === 'admin' || role === 'donor') {
                const hospital = document.getElementById('hospital')?.value.trim();
                const blood_group = document.getElementById('bloodGroup')?.value;

                if (!hospital) {
                    alert('Hospital affiliation is required.');
                    return;
                }
                if (!blood_group) {
                    alert('Blood Group is required.');
                    return;
                }
                registrationData.hospital = hospital;
                registrationData.blood_group = blood_group;
            }

            // Add donor-specific health data
            if (role === 'donor') {
                registrationData.smoking = document.querySelector('input[name="smoking"]:checked')?.value;
                registrationData.chronic_illness = document.querySelector('input[name="chronicIllness"]:checked')?.value;
                registrationData.illness_details = document.getElementById('illnessDetailsText')?.value.trim() || '';
                registrationData.tattoo = document.querySelector('input[name="tattoo"]:checked')?.value;
                registrationData.travel = document.querySelector('input[name="travel"]:checked')?.value;

                if (!registrationData.smoking || !registrationData.chronic_illness || !registrationData.tattoo || !registrationData.travel) {
                    alert('Please complete all health eligibility questions.');
                    return;
                }
            }

            // Send data to PHP backend
            fetch('../php/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(registrationData),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        form.reset();
                        roleSelect.value = '';
                        formContainer.style.display = 'none';
                        window.location.href = 'login.html';
                    } else {
                        alert("Registration failed: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert("An error occurred during registration. Please try again.");
                });
        });
    }
});
