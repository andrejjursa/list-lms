// SLOVAK MESSAGES

jQuery(function($){
	$.datepicker.regional['sk'] = {clearText: 'Vyčistiť', clearStatus: 'Vymaže nastavený dátum',
		closeText: 'Zatvoriť', closeStatus: 'Zatvoriť bez zmien',
		prevText: '<Pred.', prevStatus: 'Zobraziť predchádzajúci mesiac',
		nextText: 'Ďalší>', nextStatus: 'Zobraziť nasledujúci mesiac',
        prevBigText: '<<', prevBigStatus: 'Zobraziť predchádzajúci rok',
        nextBigText: '>>', nextBigStatus: 'Zobraziť nasledujúci rok',
		currentText: 'Teraz', currentStatus: 'Zobraziť súčasný mesiac',
		monthNames: ['Január','Február','Marec','Apríl','Máj','Jún',
		'Júl','August','September','Október','November','December'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','Máj','Jún',
		'Júl','Aug','Sep','Okt','Nov','Dec'],
		monthStatus: 'Zobraziť iný mesiac', yearStatus: 'Zobraziť iný rok',
		weekHeader: 'Ví', weekStatus: 'Víkend v roku',
		dayNames: ['Nedeľa','Pondelok','Utorok','Streda','Štvrtok','Piatok','Sobota'],
		dayNamesShort: ['Ned','Pon','Uto','Str','Štv','Pia','Sob'],
		dayNamesMin: ['Ne','Po','Ut','St','Št','Pi','So'],
		dayStatus: 'Nastavte DD ako prvý víkendový deň', dateStatus: 'Vyberte DD, MM d',
		dateFormat: 'dd:mm:yy', firstDay: 1, 
		initStatus: 'Vyberte dátum', isRTL: false};
    $.timepicker.regional['sk'] = {
        timeOnlyTitle: 'Výber času',
        timeText: 'Čas',
        hourText: 'Hodina',
    	minuteText: 'Minúta',
    	secondText: 'Sekunda',
    	millisecText: 'Milisekunda',
    	timezoneText: 'Časové pásmo',
    	currentText: 'Teraz',
    	closeText: 'Zatvoriť',
    	timeFormat: 'HH:mm:ss',
    	amNames: ['dop.', 'AM', 'A'],
    	pmNames: ['pop.', 'PM', 'P'],
    	isRTL: false
    };
});

var lang = { 
    messages: {
        ajax_standby: 'Čakajte prosím ...',
        filter_header: 'Filter',
        form_header: 'Zobraziť / skryť formulár',
        overlay_editor_header: 'Upraviť jazykové prekrytia',
        error_notification_dialog_title: 'Chyba vo formulári',
        error_notification_dialog_close: 'Zatvoriť'
    },
    dataTables: {
        'oAria': {
            'sSortAscentind': ' - klik/návrat k zostupnému zoradeniu',
            'sSortDescending': ' - klik/návrat k vzostupnému zoradeniu'
        },
        'oPaginate': {
            'sFirst': 'Prvá stránka',
            'sLast': 'Posledná stránka',
            'sNext': 'Nasledujúca stránka',
            'sPrevious': 'Predcházdajúca stránka'
        },
        'sEmptyTable': 'V tabuľke nie sú dostupné žiadne dáta.',
        'sInfo': 'Zobrazuje sa _START_ až _END_ z _TOTAL_ riadkov.',
        'sInfoEmpty': 'Nie sú dostupné žiadne záznamy.',
        'sInfoFiltered': ' Filtrované z _MAX_ riadkov.',
        'sInfoPostFix': '',
        'sLengthMenu': 'Zoraziť _MENU_ riadkov.',
        'sLoadingRecords': 'Prosím čakajte - načítava sa ...',
        'sProcessing': 'Spracovávam ...',
        'sSearch': 'Nájsť:',
        'sUrl': '',
        'sZeroRecords': 'Nenašli sa odpovedajúce záznamy.'
    }
};