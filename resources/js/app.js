import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import 'html5-qrcode';
import './server-table';

Alpine.plugin(collapse);
window.Alpine = Alpine;

Alpine.start();
