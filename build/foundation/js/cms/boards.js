import Vue from 'vue';
import Board from "./boards/components/Board";
import BoardSlot from "./boards/components/BoardSlot";

let $boards = $('div[data-vue=board');
if ($boards.length) {
    new Vue({
        el: 'div[data-vue=board]',
        components: {
            Board,
            BoardSlot
        }
    });
}
