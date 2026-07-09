import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import 'html5-qrcode';
import './server-table';
import Chart from 'chart.js/auto';

Alpine.plugin(collapse);
window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();
