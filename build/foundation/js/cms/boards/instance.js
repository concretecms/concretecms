import BoardInstanceSlot from './slot';

export default class BoardInstance {
    
    constructor(options) {
        this.element = options['element'];
        this.slots = [];

        let slots = document.querySelectorAll('[data-board-instance-slot-id]');
        for (var i = 0; i < slots.length; i++) {
            this.slots.push(new BoardInstanceSlot({
                'element': slots[i]
            }));
        }
    }
    
}
