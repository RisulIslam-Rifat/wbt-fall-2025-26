// Load pending users from backend
function loadUsers() {
    fetch('../php/admin_dashboard.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const donors = data.donors;
                const finders = data.finders;

                const pendingDonors = donors.filter(u => u.status === 'pending');
                const pendingFinders = finders.filter(u => u.status === 'pending');

                // Combine approved users
                const approvedUsers = [...donors, ...finders].filter(u => u.status === 'approved');

                const totalPending = pendingDonors.length + pendingFinders.length;
                const totalPendingEl = document.getElementById('totalPending');
                if (totalPendingEl) totalPendingEl.textContent = totalPending;

                renderTable('donorTable', pendingDonors, 'donor');
                renderTable('finderTable', pendingFinders, 'finder');
                renderTable('approvedTable', approvedUsers, 'approved');
            } else {
                console.error('Failed to load dashboard data:', data.message);
            }
        })
        .catch(error => console.error('Error loading dashboard:', error));
}

function renderTable(tableId, users, type) {
    const tbody = document.querySelector(`#${tableId} tbody`);
    tbody.innerHTML = '';

    if (users.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="py-4 text-center text-slate-500">No ${type === 'approved' ? 'approved users' : `pending ${type}s`}.</td></tr>`;
        return;
    }

    users.forEach((user) => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors';

        // Capitalize status for display
        const statusDisplay = user.status.charAt(0).toUpperCase() + user.status.slice(1);

        let statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'; // Default pending
        if (user.status === 'approved') statusClass = 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        else if (user.status === 'declined') statusClass = 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';

        const statusTag = `<span class="px-2 py-1 rounded-full text-xs font-bold ${statusClass}">${statusDisplay}</span>`;

        row.innerHTML = `
            <td class="px-6 py-4 text-sm font-medium text-[#0d141b] dark:text-white">${user.fullname}</td>
            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">${user.email}</td>
            ${type === 'donor' ? `<td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">${user.hospital || '-'}</td>` : ''}
            ${type === 'approved' ? `<td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 uppercase font-bold text-xs">${user.role || (user.hospital ? 'Donor' : 'Finder')}</td>` : ''}
            <td class="px-6 py-4 text-sm">${statusTag}</td>
            <td class="px-6 py-4 text-sm font-medium">
                <div class="flex gap-2">
                    ${user.status === 'pending' ?
                `<button onclick="updateStatus(${user.id}, 'approved')" class="text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 font-bold">Approve</button>
                     <button onclick="updateStatus(${user.id}, 'declined')" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-bold">Decline</button>` :
                `<button onclick="updateStatus(${user.id}, 'declined')" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-bold">Revoke</button>`
            }
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Update user status (Approve/Decline)
function updateStatus(userId, status) {
    let actionText = status === 'approved' ? 'approve' : 'decline';
    if (status === 'declined') actionText = 'revoke access for';

    if (!confirm(`Are you sure you want to ${actionText} this user?`)) return;

    fetch('../php/update_user_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ user_id: userId, status: status }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadUsers(); // Refresh the table
            } else {
                alert('Failed to update status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating status.');
        });
}

// Initial load
document.addEventListener('DOMContentLoaded', loadUsers);