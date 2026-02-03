function changeItem(type, direction) {
    const items = document.querySelectorAll('.' + type + 'Card');
    const total = items.length;

    if (total === 0)
        return;


    if (typeof window.startIndexes === 'undefined') {
        window.startIndexes = { 'car': 0, 'driver': 0 };
    }

    let currentIndex = window.startIndexes[type];


    let newIndex = currentIndex + direction;

   
    if (newIndex < 0) {
        newIndex = total - 1;
    } else if (newIndex >= total) {
        newIndex = 0;
    }

    items.forEach(item => {
        item.classList.remove('active');
    });

    items[newIndex].classList.add('active');
    
    window.startIndexes[type] = newIndex;
   
    const hiddenInputs = document.querySelectorAll('.live-' + type + '-index');
    hiddenInputs.forEach(input => {
        input.value = newIndex;
    });
}
