</div>
<!-- Footer section -->
<!-- Libs JS -->
<script src="<?php echo path('assets', 'dist'); ?>libs/apexcharts/dist/apexcharts.min.js" defer></script>
<!-- Datepicker -->
<script src="<?php echo path('assets', 'dist'); ?>libs/litepicker/dist/litepicker.js" defer></script>
<script src="<?php echo path('assets', 'dist'); ?>libs/jsvectormap/dist/js/jsvectormap.min.js" defer></script>
<script src="<?php echo path('assets', 'dist'); ?>libs/jsvectormap/dist/maps/world.js" defer></script>
<script src="<?php echo path('assets', 'dist'); ?>libs/jsvectormap/dist/maps/world-merc.js" defer></script>
<!-- Tabler Core -->
<script src="<?php echo path('assets', 'dist'); ?>js/tabler.min.js" defer></script>
<script src="<?php echo path('assets', 'dist'); ?>js/demo.min.js" defer></script>

<!-- Patients register page -->
<script>
let historyCount = 1;

function addMedicalHistoryField() {
    const container = document.getElementById('medical_history_container');
    const template = document.querySelector('.medical-history-entry').cloneNode(true);

    template.querySelectorAll('input, select').forEach(input => {
        input.name = input.name.replace('[0]', `[${historyCount}]`);
        input.value = '';
    });

    container.appendChild(template);
    historyCount++;
}
</script>

<!-- To toggle sidebar dropdown on or off -->
<script>
// Check localStorage on page load and apply saved state
document.addEventListener('DOMContentLoaded', function() {
    const dropdownsOpen = localStorage.getItem('dropdownsOpen') === 'true';
    const dropdowns = document.querySelectorAll('.dropdown-menu');

    if (dropdownsOpen) {
        dropdowns.forEach(dropdown => dropdown.classList.add('show'));
    }
});

// Toggle dropdowns and save state to localStorage
document.getElementById('toggleDropdowns').addEventListener('click', function() {
    const dropdowns = document.querySelectorAll('.dropdown-menu');
    const firstDropdown = dropdowns[0];
    const isCurrentlyOpen = firstDropdown.classList.contains('show');

    dropdowns.forEach(dropdown => {
        if (isCurrentlyOpen) {
            dropdown.classList.remove('show');
        } else {
            dropdown.classList.add('show');
        }
    });

    // Save state to localStorage
    localStorage.setItem('dropdownsOpen', (!isCurrentlyOpen).toString());
});
</script>

<!-- For the search patient page -->
<script>
function confirmDelete(patientId) {
    if (confirm('Are you sure you want to delete this patient record? This action cannot be undone.')) {
        window.location.href = `delete_patient.php?id=${patientId}`;
    }
}
</script>

<!-- AJAX Navigation to prevent page reloading every time I click the sidebar menus -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle all dropdown menu item clicks
    document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;

            // Fetch the new page content
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    // Extract the main content area from the response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.querySelector('.page-wrapper') || doc
                        .querySelector('main');

                    // Update the current page's content
                    const currentContent = document.querySelector('.page-wrapper') ||
                        document.querySelector('main');
                    if (currentContent && newContent) {
                        currentContent.innerHTML = newContent.innerHTML;

                        // Update the URL without reloading
                        history.pushState({}, '', url);

                        // Re-initialize any necessary scripts for the new content
                        if (typeof initializePage === 'function') {
                            initializePage();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading page:', error);
                    window.location.href = url; // Fallback to normal navigation
                });
        });
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        location.reload();
    });
});
</script>

<!-- Insurance Provider lists -->
<script>
function toggleStatus(providerId) {
    if (confirm('Are you sure you want to change this provider\'s status?')) {
        fetch('../../handlers/toggle_insurance_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'provider_id=' + providerId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
    }
}
</script>

<!-- For the Patient's list -->
<script>
function deletePatient(patientId) {
    if (confirm('Are you sure you want to delete this patient?')) {
        fetch('../../handlers/delete_patient.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'patient_id=' + patientId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the patient');
            });
    }
}
</script>

<!-- For Queue Search -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const patientIdInput = document.getElementById('patient_id');
    const searchButton = document.getElementById('search_patient');
    const patientDetailsDiv = document.getElementById('patient_details');

    function searchPatient() {
        const patientId = patientIdInput.value.trim();
        if (!patientId) {
            patientDetailsDiv.innerHTML = '';
            return;
        }

        fetch(`../../handlers/get_patient_details.php?patient_id=${encodeURIComponent(patientId)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const patient = data.data;
                    patientDetailsDiv.innerHTML = `
                        <div class="alert alert-success-custom mb-0">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h4 class="alert-title mb-1">${patient.first_name} ${patient.last_name}</h4>
                                    <div class="text-muted">Patient ID: ${patient.patient_number}</div>
                                </div>
                            </div>
                        </div>`;
                    // Update hidden input with internal ID if needed
                    if (patient.id !== patientId) {
                        patientIdInput.value = patient.id;
                    }
                } else {
                    patientDetailsDiv.innerHTML = `
                        <div class="alert alert-danger-custom mb-0">
                            <div class="d-flex align-items-center">
                                <div>Patient not found</div>
                            </div>
                        </div>`;
                }
            })
            .catch(error => {
                patientDetailsDiv.innerHTML = `
                    <div class="alert alert-danger mb-0">
                        <div class="d-flex align-items-center">
                            <div>Error searching for patient</div>
                        </div>
                    </div>`;
                console.error('Error:', error);
            });
    }

    // Search when button is clicked
    searchButton.addEventListener('click', searchPatient);

    // Also search when Enter is pressed
    patientIdInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchPatient();
        }
    });
});
</script>

<!-- Search patient button -->
<script>
document.getElementById('search_patient').addEventListener('click', function() {
    const patientId = document.getElementById('patient_id').value;
    if (!patientId) return;

    fetch(`../../handlers/get_patient.php?patient_id=${encodeURIComponent(patientId)}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('patient_details').innerHTML =
                    `<div class="alert alert-danger">${data.error}</div>`;
            } else {
                document.getElementById('patient_details').innerHTML =
                    `<div class="alert alert-success">
                        Patient: ${data.first_name} ${data.last_name}<br>
                        Patient ID: ${data.patient_number}
                    </div>`;
            }
        })
        .catch(error => {
            document.getElementById('patient_details').innerHTML =
                `<div class="alert alert-danger">Error searching for patient</div>`;
        });
});
</script>

<!-- The create billing -->
<script>
let itemCount = 1;

function addBillItem() {
    const template = document.querySelector('.bill-item').cloneNode(true);
    template.querySelectorAll('select, input').forEach(element => {
        element.name = element.name.replace('[0]', `[${itemCount}]`);
        if (!element.readOnly) element.value = '';
    });
    document.getElementById('bill_items').appendChild(template);
    itemCount++;
    updateTotalAmount();
}

function updateItemPrice(select) {
    const row = select.closest('.bill-item');
    const type = row.querySelector('.item-type').value;
    const itemId = select.value;

    if (type && itemId) {
        fetch(`../../handlers/get_item_price.php?type=${type}&id=${itemId}`)
            .then(response => response.json())
            .then(data => {
                row.querySelector('.item-price').value = data.price;
                updateSubtotal(row);
            });
    }
}

function updateSubtotal(row) {
    const quantity = row.querySelector('.item-quantity').value;
    const price = row.querySelector('.item-price').value;
    const subtotal = quantity * price;
    row.querySelector('.item-subtotal').value = subtotal.toFixed(2);
    updateTotalAmount();
}

function updateTotalAmount() {
    const subtotals = Array.from(document.querySelectorAll('.item-subtotal'))
        .map(input => parseFloat(input.value) || 0);
    const total = subtotals.reduce((sum, value) => sum + value, 0);
    document.getElementById('total_amount').textContent = total.toFixed(2);
    document.getElementById('total_amount_input').value = total.toFixed(2);
}

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('item-type')) {
        const row = e.target.closest('.bill-item');
        const itemSelect = row.querySelector('.item-select');
        const type = e.target.value;

        itemSelect.innerHTML = '<option value="">Loading...</option>';
        fetch(`../../handlers/get_items.php?type=${type}`)
            .then(response => response.json())
            .then(data => {
                itemSelect.innerHTML = '<option value="">Select Item</option>' +
                    data.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
            });
    } else if (e.target.classList.contains('item-select')) {
        updateItemPrice(e.target);
    } else if (e.target.classList.contains('item-quantity')) {
        updateSubtotal(e.target.closest('.bill_item'));
    }
});

// Update appointments when patient is selected
document.querySelector('select[name="patient_id"]').addEventListener('change', function(e) {
    const appointmentSelect = document.querySelector('select[name="appointment_id"]');
    const patientId = e.target.value;

    if (patientId) {
        appointmentSelect.innerHTML = '<option value="">Loading...</option>';
        fetch(`../../handlers/get_appointments.php?patient_id=${patientId}`)
            .then(response => response.json())
            .then(data => {
                appointmentSelect.innerHTML = '<option value="">Select Appointment</option>' +
                    data.map(apt =>
                        `<option value="${apt.id}">${apt.appointment_datetime} - ${apt.reason}</option>`)
                    .join('');
            });
    }
});
</script>

<!-- For Register Outpatient -->
<script>
function searchPatient() {
    const searchTerm = document.getElementById('patient_search').value;
    if (!searchTerm) {
        alert('Please enter a search term');
        return;
    }

    fetch('../../handlers/api/outpatient_search_patient.php?term=' + encodeURIComponent(searchTerm))
        .then(response => response.json())
        .then(data => {
            if (data.success && data.patient) {
                const patient = data.patient;
                // Update form fields with patient data
                document.getElementById('patient_id').value = patient.id;
                document.getElementById('patient_name').value = `${patient.first_name} ${patient.last_name}`;
                document.getElementById('patient_number').value = patient.patient_id;
                document.getElementById('patient_phone').value = patient.phone;

                // Show the patient details section
                document.getElementById('patient_details').style.display = 'flex';
            } else {
                alert('No patient found with the given search term');
                // Clear and hide patient details if no patient found
                document.getElementById('patient_details').style.display = 'none';
                document.getElementById('patient_id').value = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error searching for patient');
        });
}

// Add event listener for Enter key in search field
document.getElementById('patient_search').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        searchPatient();
    }
});
</script>

<!-- For medications list view -->
<script>
function updateStock(id) {
    document.getElementById('medication_id').value = id;
    new bootstrap.Modal(document.getElementById('stockUpdateModal')).show();
}
</script>

<!-- For the appointments part -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today
    const dateInput = document.querySelector('input[name="appointment_datetime"]');
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const hh = String(today.getHours()).padStart(2, '0');
    const min = String(today.getMinutes()).padStart(2, '0');

    dateInput.min = `${yyyy}-${mm}-${dd}T${hh}:${min}`;
});
</script>

<!-- For the appointments list view part -->
<script>
function confirmDelete(appointmentId) {
    if (confirm('Are you sure you want to delete this appointment?')) {
        window.location.href = `../../handlers/delete_appointment.php?id=${appointmentId}`;
    }
}
</script>

<!-- For the check in in appointments view -->
<script>
function checkInPatient(appointmentId) {
    if (confirm('Check in this patient?')) {
        fetch('../../handlers/update_appointment_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `appointment_id=${appointmentId}&status=checked_in`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to check in patient');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while checking in the patient');
            });
    }
}
</script>

<!-- For the vital signs part of the check in -->
<script>
function updateVitalSignStatus(type, value) {
    const statusElement = document.getElementById(`${type}_status`);

    if (!value) {
        statusElement.textContent = '';
        statusElement.className = 'ms-2 vital-status';
        return;
    }

    let message = '';
    let isHigh = false;

    switch (type) {
        case 'blood_pressure':
            const bpParts = value.split('/');
            if (bpParts.length === 2) {
                const systolic = parseInt(bpParts[0]);
                const diastolic = parseInt(bpParts[1]);
                if (systolic && diastolic) {
                    if (systolic > 140 || diastolic > 90) {
                        message = 'High blood pressure';
                        isHigh = true;
                    } else if (systolic < 90 || diastolic < 60) {
                        message = 'Low blood pressure';
                        isHigh = true;
                    } else {
                        message = 'Normal';
                    }
                }
            }
            break;
        case 'temperature':
            const temp = parseFloat(value);
            if (!isNaN(temp)) {
                if (temp > 37.5) {
                    message = 'High temperature';
                    isHigh = true;
                } else if (temp < 35.5) {
                    message = 'Low temperature';
                    isHigh = true;
                } else {
                    message = 'Normal';
                }
            }
            break;
        case 'heart_rate':
            const rate = parseInt(value);
            if (!isNaN(rate)) {
                if (rate > 100) {
                    message = 'High heart rate';
                    isHigh = true;
                } else if (rate < 60) {
                    message = 'Low heart rate';
                    isHigh = true;
                } else {
                    message = 'Normal';
                }
            }
            break;
    }

    if (statusElement) {
        statusElement.textContent = message;
        statusElement.className = `ms-2 vital-status ${isHigh ? 'text-danger' : 'text-success'} fw-bold`;
    }
}
</script>

<!-- For the view consultations part -->
<script>
document.getElementById('consultationForm').addEventListener('submit', function(e) {
    if (!confirm('Are you sure you want to save these changes?')) {
        e.preventDefault();
    }
});
</script>

<!-- For the waiting room part -->
<script>
function prepareConsultation(patientId, queueId) {
    document.getElementById('modalPatientId').value = patientId;
    document.getElementById('modalQueueId').value = queueId;
}
</script>

<!-- For the add room -->
<script>
// Form validation
(function() {
    'use strict'

    // Fetch all forms that need validation
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
})()

// Additional client-side validation
document.querySelector('form').addEventListener('submit', function(e) {
    const roomNumber = document.querySelector('input[name="room_number"]').value;
    const capacity = document.querySelector('input[name="capacity"]').value;
    
    // Validate room number format
    if (!/^[A-Za-z0-9-]{1,20}$/.test(roomNumber)) {
        e.preventDefault();
        alert('Room number can only contain letters, numbers, and hyphens');
        return;
    }

    // Validate capacity
    if (capacity < 1 || capacity > 20) {
        e.preventDefault();
        alert('Capacity must be between 1 and 20');
        return;
    }
});
</script> 

<!-- For the prescriptions list -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete prescription functionality
    const deleteButtons = document.querySelectorAll('.delete-prescription');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const prescriptionId = this.getAttribute('data-id');

            if (confirm('Are you sure you want to delete this prescription?')) {
                fetch(`delete.php?id=${prescriptionId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                    // Remove the row from the table
                    this.closest('tr').remove();
                    alert('Prescription deleted successfully');
                } else {
                    alert('Failed to delete prescription: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the prescription');
            });
        }
        });
    });
});    
</script> 

<!-- For the items list view -->
<script>
$(document).ready(function() {
    // Handle Update Price button click
    $('.update-price-btn').click(function() {
        const itemId = $(this).data('item-id');
        const currentPrice = $(this).data('current-price');

        $('#update-item-id').val(itemId);
        $('#current-price').val(currentPrice);
        $('#updatePriceModal').moda('show');
    });

    // Handle Price Update Form Submit
    $('#updatePriceForm').submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: '../../handlers/update_price.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', 'Price updated successfully');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred while updating the price');
            }
        });
    });

    // Handle View History button click
    $('.view-history-btn').click(function() {
        const itemId = $(this).data('item-id');

        $.ajax({
            url: '../../handlers/get_price_history.php',
            type: 'GET',
            data: { item_id: itemId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const tbody = $('#price-history-body');
                    tbody.empty();

                    response.history.forEach(function(record) {
                        tbody.append(`
                            <tr>
                                <td>${record.change_date}</td>
                                <td>${parseFloat(record.old_price).toFixed(2)}</td>
                                <td>${parseFloat(record.new_price).toFixed(2)}</td>
                                <td>${record.changed_by_user}</td>
                                <td>${record.notes || ''}</td>
                            </tr>
                        `);
                    });

                    $('#priceHistoryModal').modal('show');
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred while fetching price history');
            }
        });
    });
});

function showAlert(type, message) {
    const alertDiv = $(`<div class="alert alert-${type} alert-dismissible" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`);

    $('.page-body').prepend(alertDiv);
    setTimeout(() => alertDiv.alert('close'), 3000);
}
</script> 

<!-- For the add/edit item form -->
<script>
$(document).ready(function() {
    $('form').submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(() => window.location.href = 'items.php', 1500);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred while saving the item');
            }
        });
    });
});

function showAlert(type, message) {
    const alertDiv = $(`<div class="alert alert-${type} alert-dismissible" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`);

    $('.page-body').prepend(alertDiv);
    setTimeout(() => alertDiv.alert('close'), 3000);
}
</script>

<!-- For the pharmacy pending prescriptions view -->
<script>
function showDispensingModal(prescription) {
    const modal = document.getElementById('dispensingModal');
    modal.querySelector('#prescription_id').value = prescription.id;
    
    // Show the modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}
</script> 

<!-- For register user view -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const doctorCheckbox = document.getElementById('role_doctor');
    const doctorFields = document.getElementById('doctor-fields');
    
    // Function to toggle required attribute on doctor fields
    function toggleDoctorFieldsRequired(required) {
        const doctorInputs = doctorFields.querySelectorAll('input');
        doctorInputs.forEach(input => {
            input.required = required;
        });
    }

    // Initial check
    if (doctorCheckbox.checked) {
        doctorFields.style.display = 'block';
        toggleDoctorFieldsRequired(true);
    }

    // Add change event listener
    doctorCheckbox.addEventListener('change', function() {
        if (this.checked) {
            doctorFields.style.display = 'block';
            toggleDoctorFieldsRequired(true);
        } else {
            doctorFields.style.display = 'none';
            toggleDoctorFieldsRequired(false);
        }
    });
});
</script>

</body>

</html>
<!-- End of footer section -->