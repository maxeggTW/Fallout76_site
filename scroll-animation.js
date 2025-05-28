document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');
    
    // 獲取所有需要淡入的元素
    const fadeElements = document.querySelectorAll('.content-text, .side-image, .contentR-text, .contentL-text');
    console.log('Found elements:', fadeElements.length);
    
    // 添加fade-in類
    fadeElements.forEach((element, index) => {
        element.classList.add('fade-in');
        console.log(`Added fade-in class to element ${index}:`, element);
    });

    // 創建Intersection Observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            console.log('Element intersection status:', entry.isIntersecting);
            // 當元素進入視窗時
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                console.log('Added visible class to:', entry.target);
            } else {
                entry.target.classList.remove('visible');
                console.log('Removed visible class from:', entry.target);
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: '50px' // 調整為更大的值，使動畫更早觸發
    });

    // 開始觀察所有元素
    fadeElements.forEach(element => {
        observer.observe(element);
        console.log('Started observing:', element);
    });
}); 