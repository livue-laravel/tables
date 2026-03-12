/**
 * Primix Tables - PrimeVue Data Components
 *
 * Registers components used by table filters.
 * Tables use Blade views with standard HTML (not PrimeVue DataTable).
 */

import DatePicker from 'primevue/datepicker';
import Select from 'primevue/select';
import SelectButton from 'primevue/selectbutton';
import LiVue from 'livue';
import { ensurePrimeVueTheme } from '@primix/support/primix';

import '../css/index.css';

const registerTablesComponents = (app) => {
    if (app?.config?.globalProperties?.__primixTablesReady) {
        return;
    }

    app.config.globalProperties.__primixTablesReady = true;

    ensurePrimeVueTheme(app);

    app.component('PDatePicker', DatePicker);
    app.component('PSelect', Select);
    app.component('PSelectButton', SelectButton);
};

LiVue.setup(registerTablesComponents);
