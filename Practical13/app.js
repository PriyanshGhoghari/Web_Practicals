const form = document.querySelector('#student-form');
const table = document.querySelector('#students');
const message = document.querySelector('#message');

function showMessage(text, type = '') {
    message.textContent = text;
    message.className = type;
}


async function api(data) {
    const response = await fetch('api.php', data ? { method: 'POST', body: data } : {});
    const result = await response.json();
    if (!result.success) throw new Error(result.message);
    return result;
}


async function loadStudents() {
    const result = await api();
    table.innerHTML = '';

    for (const student of result.students) {
        const row = table.insertRow();
        for (const value of [student.id, student.name, student.email, student.course]) {
            row.insertCell().textContent = value;
        }

        const actions = row.insertCell();
        const edit = document.createElement('button');
        edit.textContent = 'Edit';
        edit.onclick = () => editStudent(student);

        const del = document.createElement('button');
        del.textContent = 'Delete';
        del.className = 'delete';
        del.onclick = () => deleteStudent(student.id);
        actions.append(edit, ' ', del);
    }

    document.querySelector('#count').textContent = result.students.length + ' students found';
    if (result.students.length === 0) {
        const cell = table.insertRow().insertCell();
        cell.colSpan = 5;
        cell.textContent = 'No students found.';
    }
}

async function refreshStudents() {
    showMessage('Loading students...');
    try {
        await loadStudents();
        showMessage('Students loaded!', 'success');
    } catch (error) {
        showMessage(error.message, 'error');
    }
}

function resetForm() {
    form.reset();
    form.elements.id.value = '';
    document.querySelector('#form-title').textContent = 'Add Student';
    document.querySelector('#save').textContent = 'Add Student';
}

function editStudent(student) {
    form.elements.id.value = student.id;
    form.elements.name.value = student.name;
    form.elements.email.value = student.email;
    form.elements.course.value = student.course;
    document.querySelector('#form-title').textContent = 'Edit Student';
    document.querySelector('#save').textContent = 'Update Student';
    form.elements.name.focus();
}

form.onsubmit = async (event) => {
    event.preventDefault(); 
    const data = new FormData(form);
    data.set('action', form.elements.id.value ? 'update' : 'add');
    showMessage('Saving student...');
    try {
        const result = await api(data);
        await loadStudents();
        resetForm();
        showMessage(result.message, 'success');
    } catch (error) {
        showMessage(error.message, 'error');
    }
};

async function deleteStudent(id) {
    if (!confirm('Delete this student?')) return;
    const data = new FormData();
    data.set('action', 'delete');
    data.set('id', id);
    showMessage('Deleting student...');
    try {
        const result = await api(data);
        await loadStudents();
        resetForm();
        showMessage(result.message, 'success');
    } catch (error) {
        showMessage(error.message, 'error');
    }
}

document.querySelector('#cancel').onclick = resetForm;
document.querySelector('#refresh').onclick = refreshStudents;
refreshStudents();
