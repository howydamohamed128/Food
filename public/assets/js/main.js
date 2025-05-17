// document.addEventListener('DOMContentLoaded', () => {
//     // Get DOM elements
//     const categoriesContainer = document.getElementById('categories-container');
//     const menuItemsContainer = document.getElementById('menu-items-container');
//     const activeCategoryTitle = document.getElementById('active-category-title');
    
//     let menuData = null;
//     let activeCategory = 'all'; // Default to show all items
    
//     // Fetch menu data
//     // fetchMenuData();
    
//     /**
//      * Fetch menu data from JSON file
//      */
//     async function fetchMenuData() {
//         // try {
//         //     const response = await fetch('/static/data/menu.json');
//         //     menuData = await response.json();
            
//         //     // Render categories and initial menu items
//         //     renderCategories();
//         //     renderMenuItems();
//         // } catch (error) {
//         //     console.error('Error fetching menu data:', error);
//         //     categoriesContainer.innerHTML = '<div class="alert alert-danger">Failed to load menu data. Please try again later.</div>';
//         //     menuItemsContainer.innerHTML = '<div class="alert alert-danger">Failed to load menu items. Please try again later.</div>';
//         // }
//     }
    
//     /**
//      * Render category cards
//      */
//     function renderCategories() {
//         if (!menuData || !menuData.categories) return;
        
//         // Clear loading spinner
//         categoriesContainer.innerHTML = '';
        
//         // Add "All" category first
//         const allCategoryCard = createCategoryCard({
//             id: 'all',
//             name: 'All Items',
//             image: 'https://source.unsplash.com/random/300x200/?food'
//         }, activeCategory === 'all');
        
//         categoriesContainer.appendChild(allCategoryCard);
        
//         // Add rest of categories
//         menuData.categories.forEach(category => {
//             const categoryCard = createCategoryCard(category, activeCategory === category.id);
//             categoriesContainer.appendChild(categoryCard);
//         });
//     }
    
//     /**
//      * Create a category card element
//      */
//     function createCategoryCard(category, isActive) {
//         const card = document.createElement('div');
//         card.classList.add('category-card');
//         if (isActive) {
//             card.classList.add('active');
//         }
//         card.setAttribute('data-category-id', category.id);
        
//         const cardContent = `
//             <div class="category-img" style="background: url('${category.image}') center/cover no-repeat;"></div>
//             <div class="category-name">${category.name}</div>
//         `;
        
//         card.innerHTML = cardContent;
        
//         // Add click event
//         card.addEventListener('click', () => {
//             activeCategory = category.id;
//             activeCategoryTitle.textContent = category.name;
            
//             // Update active category styling
//             document.querySelectorAll('.category-card').forEach(card => {
//                 card.classList.remove('active');
//             });
//             card.classList.add('active');
            
//             // Render filtered menu items
//             renderMenuItems();
//         });
        
//         return card;
//     }
    
//     /**
//      * Render menu items based on active category
//      */
//     function renderMenuItems() {
//         if (!menuData || !menuData.items) return;
        
//         // Clear loading spinner
//         menuItemsContainer.innerHTML = '';
        
//         // Filter items by active category
//         let filteredItems = menuData.items;
//         if (activeCategory !== 'all') {
//             filteredItems = menuData.items.filter(item => item.categoryId === activeCategory);
//         }
        
//         // Display message if no items found
//         if (filteredItems.length === 0) {
//             menuItemsContainer.innerHTML = '<div class="alert alert-info">No items found in this category.</div>';
//             return;
//         }
        
//         // Render each menu item
//         filteredItems.forEach(item => {
//             const menuItem = createMenuItem(item);
//             menuItemsContainer.appendChild(menuItem);
//         });
//     }
    
//     /**
//      * Create a menu item element
//      */
//     function createMenuItem(item) {
//         const menuItem = document.createElement('div');
//         menuItem.classList.add('menu-item');
        
//         // Find the category name for this item
//         let categoryName = 'Uncategorized';
//         if (menuData.categories) {
//             const category = menuData.categories.find(cat => cat.id === item.categoryId);
//             if (category) {
//                 categoryName = category.name;
//             }
//         }
        
//         // Create tags HTML
//         const tagsHTML = item.tags && item.tags.length
//             ? item.tags.map(tag => `<span class="tag">${tag}</span>`).join('')
//             : '';
        
//         const menuItemContent = `
//             <div class="menu-item-img" style="background: url('${item.image}') center/cover no-repeat;"></div>
//             <div class="menu-item-info">
//                 <div class="menu-item-header">
//                     <h4 class="menu-item-name">${item.name}</h4>
//                     <p class="menu-item-price">$${item.price.toFixed(2)}</p>
//                 </div>
//                 <p class="menu-item-description">${item.description}</p>
//                 <div class="menu-item-tags">
//                     <span class="tag">${categoryName}</span>
//                     ${tagsHTML}
//                 </div>
//             </div>
//         `;
        
//         menuItem.innerHTML = menuItemContent;
//         return menuItem;
//     }
// });
