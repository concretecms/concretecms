function ConcreteBoardSlotMenu($element, options) {

	ConcreteMenu.call(this, $element, options);
}

ConcreteBoardSlotMenu.prototype = Object.create(ConcreteMenu.prototype);


ConcreteBoardSlotMenu.prototype.setupMenuOptions = function($menu) {
	
};

global.ConcreteBoardSlotMenu = ConcreteBoardSlotMenu;
