document.addEventListener('DOMContentLoaded', () => {
    const apiUrl = 'api.php';
    const itemForm = document.getElementById('add-item-form');
    const inventoryBody = document.getElementById('inventory-body');
    const itemIdInput = document.getElementById('item-id');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');

    // Fetch and display all items on page load
    fetchItems();

    // --- FUNCTIONS ---

    /**
     * Fetches all inventory items from the API and populates the table
     */
    function fetchItems() {
        fetch(`${apiUrl}?action=get_items`)
            .then(response => response.json())
            .then(data => {
                inventoryBody.innerHTML = ''; // Clear existing table rows
                if (data.length > 0) {
                    data.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${escapeHTML(item.name)}</td>
                            <td>${escapeHTML(item.description)}</td>
                            <td>${item.quantity}</td>
                            <td>$${parseFloat(item.price).toFixed(2)}</td>
                            <td>${new Date(item.created_at).toLocaleString()}</td>
                            <td>
                                <button class="edit-btn" data-id="${item.id}">Edit</button>
                                <button class="delete-btn" data-id="${item.id}">Delete</button>
                            </td>
                        `;
                        inventoryBody.appendChild(row);
                    });
                } else {
                    inventoryBody.innerHTML = '<tr><td colspan="6">No items in inventory.</td></tr>';
                }
            })
            .catch(error => console.error('Error fetching items:', error));
    }

    /**
     * Handles form submission for both adding and updating items
     * @param {Event} e The form submission event
     */
    function handleFormSubmit(e) {
        e.preventDefault();
        const formData = new FormData(itemForm);
        const id = itemIdInput.value;
        const action = id ? 'update_item' : 'add_item';
        formData.append('action', action);

        fetch(apiUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                fetchItems(); // Refresh the table
                resetForm();
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(error => console.error('Error submitting form:', error));
    }

    /**
     * Handles clicks on the table body, delegating to edit or delete buttons
     * @param {Event} e The click event
     */
    function handleTableClick(e) {
        const target = e.target;
        const id = target.dataset.id;

        if (target.classList.contains('delete-btn')) {
            if (confirm('Are you sure you want to delete this item?')) {
                deleteItem(id);
            }
        }

        if (target.classList.contains('edit-btn')) {
            prepareEditForm(id);
        }
    }

    /**
     * Deletes an item by its ID
     * @param {number} id The ID of the item to delete
     */
    function deleteItem(id) {
        const formData = new FormData();
        formData.append('action', 'delete_item');
        formData.append('id', id);

        fetch(apiUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                fetchItems(); // Refresh the table
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(error => console.error('Error deleting item:', error));
    }

    /**
     * Fetches a single item's data and populates the form for editing
     * @param {number} id The ID of the item to edit
     */
    function prepareEditForm(id) {
        // Find the item data from the currently displayed rows to avoid another fetch
        const row = document.querySelector(`.edit-btn[data-id='${id}']`).closest('tr');
        const cells = row.getElementsByTagName('td');
        
        itemIdInput.value = id;
        document.getElementById('item-name').value = cells[0].textContent;
        document.getElementById('item-description').value = cells[1].textContent;
        document.getElementById('item-quantity').value = cells[2].textContent;
        document.getElementById('item-price').value = cells[3].textContent.replace('$', '');

        itemForm.querySelector('button[type="submit"]').textContent = 'Update Item';
        cancelEditBtn.style.display = 'inline-block';
        window.scrollTo(0, 0); // Scroll to the top to see the form
    }

    /**
     * Resets the form to its initial state for adding new items
     */
    function resetForm() {
        itemForm.reset();
        itemIdInput.value = '';
        itemForm.querySelector('button[type="submit"]').textContent = 'Save Item';
        cancelEditBtn.style.display = 'none';
    }

    /**
     * Simple HTML sanitizer to prevent XSS
     * @param {string} str The string to escape
     * @returns {string} The escaped string
     */
    function escapeHTML(str) {
        const p = document.createElement('p');
        p.appendChild(document.createTextNode(str));
        return p.innerHTML;
    }


    // --- EVENT LISTENERS ---
    itemForm.addEventListener('submit', handleFormSubmit);
    inventoryBody.addEventListener('click', handleTableClick);
    cancelEditBtn.addEventListener('click', resetForm);
});
