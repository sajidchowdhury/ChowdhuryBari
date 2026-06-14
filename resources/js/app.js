import './bootstrap';

function initMobileMenu() {
	const drawer = document.getElementById('mobile-drawer');
	const menuButton = document.getElementById('mobile-menu-btn');

	if (!drawer || !menuButton) {
		return;
	}

	menuButton.addEventListener('click', () => {
		drawer.classList.toggle('hidden');
	});

	window.closeMobileMenu = () => drawer.classList.add('hidden');
	window.openMobileMenu = () => drawer.classList.remove('hidden');
}

window.openDeliveryFinder = () => {
	console.warn('openDeliveryFinder() not implemented yet');
};

window.openMemberLoginModal = () => {
	console.warn('openMemberLoginModal() not implemented yet');
};


document.addEventListener('DOMContentLoaded', initMobileMenu);



