class ArticleManager {
    constructor() {
        this.initEventListeners();
    }

    initEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.checkTextOverflow();
            
            window.addEventListener('resize', () => {
                setTimeout(() => this.checkTextOverflow(), 100);
            });
            
            document.querySelectorAll('.expand-btn').forEach(btn => {
                btn.addEventListener('click', (e) => this.toggleExpand(e));
            });
            
            const successMessage = sessionStorage.getItem('successMessage');
            if (successMessage) {
                this.showSuccessMessage(successMessage);
                sessionStorage.removeItem('successMessage');
            }
            
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                const id = e.currentTarget.closest('.article-item').dataset.id;
                    sessionStorage.setItem('successMessage', 'News was deleted!');
                    this.deleteArticle(id);
                });
            });
            
            const articleForm = document.getElementById('articleForm');
            if (articleForm) {
                articleForm.onsubmit = (e) => this.handleSubmit(e);
            }
        });
    }

    editArticle(id) {
        fetch(`/admin/article/${id}`)
            .then(response => response.json())
            .then(article => {
                document.getElementById('articleId').value = article.id;
                document.getElementById('title').value = article.title;
                document.getElementById('description').value = article.description;
                document.getElementById('formTitle').textContent = 'Edit News';
                document.getElementById('submitBtn').textContent = 'Save';
                document.getElementById('cancelBtn').style.display = 'inline-block';
                document.getElementById('articleForm').onsubmit = (e) => this.handleSubmit(e, id);
            });
    }

    deleteArticle(id) {
        const csrfToken = document.getElementById('csrf_token').value;
        
        fetch(`/admin/article/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error(`Unexpected content type: ${contentType}`);
            }
            
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || `HTTP error! Status: ${response.status}`);
                });
            }
            
            return response.json();
        })
        .then(data => {
            if (!data.success && data.error) {
                throw new Error(data.error);
            }
            
            setTimeout(() => {
                window.location.reload();
            }, 500);
        })
        .catch(error => {
            console.error('Error:', error);
            alert(`Error deleting article: ${error.message}`);
        });
    }

    resetForm() {
        document.getElementById('articleId').value = '';
        document.getElementById('articleForm').reset();
        document.getElementById('formTitle').textContent = 'Create News';
        document.getElementById('submitBtn').textContent = 'Create';
        document.getElementById('cancelBtn').style.display = 'none';
        document.getElementById('articleForm').onsubmit = (e) => this.handleSubmit(e);
    }

    handleSubmit(event, id = null) {
        event.preventDefault();
        const formData = new FormData(event.target);

        const title = formData.get('title');
        const description = formData.get('description');
        const csrfToken = formData.get('csrf_token');
        
        if (!title || title.trim() === '') {
            alert('Title cannot be empty');
            return;
        }
        
        let url, method;
        
        if (id) {
            url = `/admin/article/${id}`;
            method = 'PATCH';
            
            const data = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                data.append(key, value.trim());
            }
            
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: data
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error(`Unexpected content type: ${contentType}`);
                }
                
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.error || `HTTP error! Status: ${response.status}`);
                    });
                }
                
                return response.json();
            })
            .then(data => {
                if (!data.success && data.error) {
                    throw new Error(data.error);
                }
                
                const message = "News was successfully changed!";
                sessionStorage.setItem('successMessage', message);
                this.resetForm();
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            })
            .catch(error => {
                console.error('Error:', error);
                alert(`Error updating article: ${error.message}`);
            });
        } else {
            url = '/admin/article/create';
            method = 'POST';

            const cleanFormData = new FormData();
            for (const [key, value] of formData.entries()) {
                cleanFormData.append(key, value.trim());
            }
            
            fetch(url, {
                method: method,
                body: cleanFormData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error(`Unexpected content type: ${contentType}`);
                }
                
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.error || `HTTP error! Status: ${response.status}`);
                    });
                }
                
                return response.json();
            })
            .then(data => {
                if (!data.success && data.error) {
                    throw new Error(data.error);
                }
                
                const message = "News was successfully created!";
                sessionStorage.setItem('successMessage', message);
                this.resetForm();
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            })
            .catch(error => {
                console.error('Error:', error);
                alert(`Error creating article: ${error.message}`);
            });
        }
    }

    showSuccessMessage(message) {
        const existingMessage = document.querySelector('.success-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        const messageElement = document.createElement('div');
        messageElement.className = 'success-message';
        messageElement.textContent = message;
        
        const header = document.querySelector('.header');
        if (header) {
            header.parentNode.insertBefore(messageElement, header.nextSibling);
        } else {
            const container = document.querySelector('.container');
            if (container && container.firstChild) {
                container.insertBefore(messageElement, container.firstChild);
            } else {
                document.body.appendChild(messageElement);
            }
        }
    }

    checkTextOverflow() {
        document.querySelectorAll('.article-item').forEach(item => {
            const titleElement = item.querySelector('h3');
            const contentElement = item.querySelector('p');
            
            const titleExpandBtn = titleElement.parentElement.querySelector('.expand-btn');
            const contentExpandBtn = contentElement.parentElement.querySelector('.expand-btn');
            
            const titleWrapper = titleElement.parentElement;
            const contentWrapper = contentElement.parentElement;
            
            const isTitleOverflowing = titleElement.scrollWidth > titleElement.clientWidth;
            const isContentOverflowing = contentElement.scrollWidth > contentElement.clientWidth;
            
            if (isTitleOverflowing || titleWrapper.classList.contains('expanded')) {
                titleExpandBtn.style.display = 'flex';
            } else {
                titleExpandBtn.style.display = 'none';
            }
            
            if (isContentOverflowing || contentWrapper.classList.contains('expanded')) {
                contentExpandBtn.style.display = 'flex';
            } else {
                contentExpandBtn.style.display = 'none';
            }
        });
    }

    toggleExpand(event) {
        const wrapper = event.currentTarget.parentElement;
        wrapper.classList.toggle('expanded');
        
        setTimeout(() => this.checkTextOverflow(), 100);
        
        event.stopPropagation();
    }
}

const articleManager = new ArticleManager();

window.editArticle = (id) => articleManager.editArticle(id);
window.resetForm = () => articleManager.resetForm();
window.handleSubmit = (event) => articleManager.handleSubmit(event);
window.deleteArticle = (id) => articleManager.deleteArticle(id); 