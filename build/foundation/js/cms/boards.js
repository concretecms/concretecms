
import BoardInstance from './boards/instance';

let $$ = document.querySelectorAll.bind(document),
    instances = $$('div[data-board-instance-id]');

if (instances) {
    for (var i = 0; i < instances.length; i++) {
        let instance = new BoardInstance({
            'element': instances[i]
        })
        console.log(instance);
    }
}
