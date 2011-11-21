<? 
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Grabs a list of states and provinces commonly used in web forms.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 
defined('C5_EXECUTE') or die("Access Denied.");
class ListsStatesProvincesHelper {
	
	protected $stateProvinces = array(
	'US' => array(
		'AL' => 'Alabama',
		'AK' => 'Alaska',
		'AZ' => 'Arizona',
		'AR' => 'Arkansas',
		'CA' => 'California',
		'CO' => 'Colorado',
		'CT' => 'Connecticut',
		'DE' => 'Delaware',
		'FL' => 'Florida',
		'GA' => 'Georgia',
		'HI' => 'Hawaii',
		'ID' => 'Idaho',
		'IL' => 'Illinois',
		'IN' => 'Indiana',
		'IA' => 'Iowa',
		'KS' => 'Kansas',
		'KY' => 'Kentucky',
		'LA' => 'Louisiana',
		'ME' => 'Maine',
		'MD' => 'Maryland',
		'MA' => 'Massachusetts',
		'MI' => 'Michigan',
		'MN' => 'Minnesota',
		'MS' => 'Mississippi',
		'MO' => 'Missouri',
		'MT' => 'Montana',
		'NE' => 'Nebraska',
		'NV' => 'Nevada',
		'NH' => 'New Hampshire',
		'NJ' => 'New Jersey',
		'NM' => 'New Mexico',
		'NY' => 'New York',
		'NC' => 'North Carolina',
		'ND' => 'North Dakota',
		'OH' => 'Ohio',
		'OK' => 'Oklahoma',
		'OR' => 'Oregon',
		'PA' => 'Pennsylvania',
		'RI' => 'Rhode Island',
		'SC' => 'South Carolina',
		'SD' => 'South Dakota',
		'TN' => 'Tennessee',
		'TX' => 'Texas',
		'UT' => 'Utah',
		'VT' => 'Vermont',
		'VA' => 'Virginia',
		'WA' => 'Washington',
		'DC' => 'Washington, DC',
		'WV' => 'West Virginia',
		'WI' => 'Wisconsin',
		'WY' => 'Wyoming'
	),
	
	'CA' => array(
		'AB' => 'Alberta',
		'BC' => 'British Columbia',
		'MB' => 'Manitoba',
		'NB' => 'New Brunswick',
		'NF' => 'Newfoundland',
		'NT' => 'Northwest Territories',
		'NS' => 'Nova Scotia',
		'NU' => 'Nunavut',
		'ON' => 'Ontario',
		'PE' => 'Prince Edward Island',
		'QC' => 'Quebec',
		'SK' => 'Saskatchewan',
		'YT' => 'Yukon'
	),
	
	'AU' => array(
		'AAT' => 'Australian Antarctic Territory',
		'ACT' => 'Australian Capital Territory',
		'NT' => 'Northern Territory',
		'NSW' => 'New South Wales',
		'QLD' => 'Queensland',
		'SA' => 'South Australia',
		'TAS' => 'Tasmania',
		'VIC' => 'Victoria',
		'WA' => 'Western Australia',
	),
	
	'DE' => array(
      'BW' => 'Baden-W&uuml;rttemberg',
      'BY' => 'Bayern',
      'BE' => 'Berlin',
      'BB' => 'Brandenburg',
      'HB' => 'Bremen',
      'HH' => 'Hamburg',
      'HE' => 'Hessen',
      'MV' => 'Mecklenburg-Vorpommern',
      'NI' => 'Niedersachsen',
      'NW' => 'Nordrhein-Westfalen',
      'RP' => 'Rheinland-Pfalz',
      'SL' => 'Saarland',
      'SN' => 'Sachsen',
      'ST' => 'Sachsen-Anhalt',
      'SH' => 'Schleswig-Holstein',
      'TH' => 'Th&uuml;ringen'   
   ),

   'FR' => array(
	'01' => 'Ain',
    '02' => 'Aisne',
    '03' => 'Allier',
    '04' => 'Alpes-de-Haute-Provence',
    '05' => 'Haute-Alpes',
    '06' => 'Alpes-Maritimes',
    '07' => 'Ardèche',
    '08' => 'Ardennes',
    '09' => 'Ariège',
    '10' => 'Aube',
    '11' => 'Aude',
    '12' => 'Aveyron',
    '13' => 'Bouches-du-Rhône',
    '14' => 'Calvados',
    '15' => 'Cantal',
    '16' => 'Charente',
    '17' => 'Charente-Maritime',
    '18' => 'Cher',
    '19' => 'Corrèze',
    '2A' => 'Corse-du-Sud',
    '2B' => 'Haute-Corse',
    '21' => 'Côte-d&#39;Or',
    '22' => 'Côte-d&#39;Armor',
    '23' => 'Creuse',
    '24' => 'Dordogne',
    '25' => 'Doubs',
    '26' => 'Drôme',
    '27' => 'Eure',
    '28' => 'Eure-et-Loir',
    '29' => 'Finistère',
    '30' => 'Gard',
    '31' => 'Haute-Garonne',
    '32' => 'Gers',
    '33' => 'Gironde',
    '34' => 'Hérault',
    '35' => 'Ille-et-Vilaine',
    '36' => 'Indre',
    '37' => 'Indre-et-Loire',
    '38' => 'Isère',
    '39' => 'Jura',
    '40' => 'Landes',
    '41' => 'Loir-et-Cher',
    '42' => 'Loire',
    '43' => 'Haute-Loire',
    '44' => 'Loire-Atlantique',
    '45' => 'Loiret',
    '46' => 'Lot',
    '47' => 'Lot-et-Garonne',
    '48' => 'Lozère',
    '49' => 'Maine-et-Loire',
    '50' => 'Manche',
    '51' => 'Marne',
    '52' => 'Haute-Marne',
    '53' => 'Mayenne',
    '54' => 'Meurthe-et-Moselle',
    '55' => 'Meuse',
    '56' => 'Morbihan',
    '57' => 'Moselle',
    '58' => 'Nièvre',
    '59' => 'Nord',
    '60' => 'Oise',
    '61' => 'Orne',
    '62' => 'Pas-de-Calais',
    '63' => 'Puy-de-Dôme',
    '64' => 'Pyrénées-Atlantiques',
    '65' => 'Hautes-Pyrénées',
    '66' => 'Pyrénées-Orientales',
    '67' => 'Bas-Rhin',
    '68' => 'Haut-Rhin',
    '69' => 'Rhône',
    '70' => 'Haute-Saône',
    '71' => 'Saône-et-Loire',
    '72' => 'Sarthe',
    '73' => 'Savoie',
    '74' => 'Haute-Savoie',
    '75' => 'Paris',
    '76' => 'Seine-Maritime',
    '77' => 'Seine-et-Marne',
    '78' => 'Yvelines',
    '79' => 'Deux-Sèvres',
    '80' => 'Somme',
    '81' => 'Tarn',
    '82' => 'Tarn-et-Garonne',
    '83' => 'Var',
    '84' => 'Vaucluse',
    '85' => 'Vendée',
    '86' => 'Vienne',
    '87' => 'Haute-Vienne',
    '88' => 'Vosges',
    '89' => 'Yonne',
    '90' => 'Territoire de Belfort',
    '91' => 'Essonne',
    '92' => 'Hauts-de-Seine',
    '93' => 'Seine-Saint-Denis',
    '94' => 'Val-de-Marne',
    '95' => 'Val-d&#39;Oise'
	),
	
	'UK' => array(
		'ANGLES' => 'Anglesey',
		'BRECK' => 'Brecknockshire',
		'CAERN' => 'Caernarfonshire',
		'CARMA' => 'Carmathenshire',
		'CARDIG' => 'Cardiganshire',
		'DENBIG' => 'Denbighshire',
		'FLINTS' => 'Flintshire',
		'GLAMO' => 'Glamorgan',
		'MERION' => 'Merioneth',
		'MONMOUTH' => 'Monmouthshire',
		'MONTG' => 'Mongtomeryshire',
		'PEMBR' => 'Pembrokeshire',
		'RADNOR' => 'Radnorshire',
		
		'ARBERD' => 'Aberdeenshire',
		'ANGUS' => 'Angus',
		'ARGYLL' => 'Argyllshire',
		'AYRSH' => 'Ayrshire',
		'BANFF' => 'Banffshire',
		'BERWICK' => 'Berwickshire',
		'BUTE' => 'Buteshire',
		'CROMART' => 'Cromartyshire',
		'CAITH' => 'Caithness',
		'CLACKM' => 'Clackmannanshire',
		'DUMFR' => 'Dumfriesshire',
		'DUNBART' => 'Dunbartonshire',
		'EASTL' => 'East Lothian',
		'FIFE' => 'Fife',
		'INVERN' => 'Inverness-shire',
		'KINCARD' => 'Kincardineshire',
		'KINROSS' => 'Kinross-shire',
		'KIRKCUD' => 'Kircudbrightshire',
		'LANARK' => 'Lanarkshire',
		'MIDLOTH' => 'Midlothian',
		'MORAY' => 'Morayshire',
		'NAIRN' => 'Nairnshire',
		'ORKNEY' => 'Orkeny',
		'PEEBLESS' => 'Peeblesshire',
		'PERTH' => 'Perthshire',
		'RENFREW' => 'Renfrewshire',
		'ROSSSH' => 'Ross-shire',
		'ROXBURGH' => 'Roxburghshire',
		'SELKIRK' => 'Selkirkshire',
		'SHETLAND' => 'Shetland',
		'STIRLING' => 'Stirlingshire',
		'SUTHER' => 'Sutherland',
		'WESTL' => 'West Lothian',
		'WIGTOWN' => 'Wigtownshire',
		
		'MERSEYSIDE' => 'Merseyside',
		
		'BEDS' => 'Bedfordshire',
		'LONDON' => 'London',
		'BERKS' => 'Berkshire',
		'BUCKS' => 'Buckinghamshire',
		'CAMBS' => 'Cambridgeshire',
		'CHESH' => 'Cheshire',
		'CORN' => 'Cornwall',
		'CUMB' => 'Cumberland',
		'DERBY' => 'Derbyshire',
		'DEVON' => 'Devon',
		'DORSET' => 'Dorset',
		'DURHAM' => 'Durham',
		'ESSEX' => 'Essex',
		'GLOUS' => 'Gloucestershire',
		'HANTS' => 'Hampshire',
		'HEREF' => 'Herefordshire',
		'HERTS' => 'Hertfordshire',
		'HUNTS' => 'Huntingdonshire',
		'KENT' => 'Kent',
		'LANCS' => 'Lancashire',
		'LEICS' => 'Leicestershire',
		'LINCS' => 'Lincolnshire',
		'MIDDLE' => 'Middlesex',
		'NORF' => 'Norfolk',
		'NHANTS' => 'Northamptonshire',
		'NTHUMB' => 'Northumberland',
		'NOTTS' => 'Nottinghamshire',
		'OXON' => 'Oxfordshire',
		'RUTL' => 'Rutland',
		'SHROPS' => 'Shropshire',
		'SOM' => 'Somerset',
		'STAFFS' => 'Staffordshire',
		'SUFF' => 'Suffolk',
		'SURREY' => 'Surrey',
		'SUSS' => 'Sussex',
		'WARKS' => 'Warwickshire',
		'WESTMOR' => 'Westmorland',
		'WILTS' => 'Wiltshire',
		'WORCES' => 'Worcestershire',
		'YORK' => 'Yorkshire'
	),
	
	'IE' => array(
		'CO ANTRIM' => 'County Antrim',
		'CO ARMAGH' => 'County Armagh',
		'CO DOWN' => 'County Down',
		'CO FERMANAGH' => 'County Fermanagh',
		'CO DERRY' => 'County Londonderry',
		'CO TYRONE' => 'County Tyrone',
		'CO CAVAN' => 'County Cavan',
		'CO DONEGAL' => 'County Donegal',
		'CO MONAGHAN' => 'County Monaghan',
		'CO DUBLIN' => 'County Dublin',
		'CO CARLOW' => 'County Carlow',
		'CO KILDARE' => 'County Kildare',
		'CO KILKENNY' => 'County Kilkenny',
		'CO LAOIS' => 'County Laois',
		'CO LONGFORD' => 'County Longford',
		'CO LOUTH' => 'County Louth',
		'CO MEATH' => 'County Meath',
		'CO OFFALY' => 'County Offaly',
		'CO WESTMEATH' => 'County Westmeath',
		'CO WEXFORD' => 'County Wexford',
		'CO WICKLOW' => 'County Wicklow',
		'CO GALWAY' => 'County Galway',
		'CO MAYO' => 'County Mayo',
		'CO LEITRIM' => 'County Leitrim',
		'CO ROSCOMMON' => 'County Roscommon',
		'CO SLIGO' => 'County Sligo',
		'CO CLARE' => 'County Clare',
		'CO CORK' => 'County Cork',
		'CO KERRY' => 'County Kerry',
		'CO LIMERICK' => 'County Limerick',
		'CO TIPPERARY' => 'County Tipperary',
		'CO WATERFORD' => 'County Waterford'
	),
	
	'NL' => array(
	
		'DR' => 'Drente',
		'FL' => 'Flevoland',
		'FR' => 'Friesland',
		'GL' => 'Gelderland',
		'GR' => 'Groningen',
		'LB' => 'Limburg',
		'NB' => 'Noord Brabant',
		'NH' => 'Noord Holland',
		'OV' => 'Overijssel',
		'UT' => 'Utrecht',
		'ZH' => 'Zuid Holland',
		'ZL' => 'Zeeland'
	),
	
	'BR' => array(
	
		'AC' => 'Acre',
		'AL' => 'Alagoas',
		'AM' => 'Amazonas',
		'AP' => 'Amapa',
		'BA' => 'Baia',
		'CE' => 'Ceara',
		'DF' => 'Distrito Federal',
		'ES' => 'Espirito Santo',
		'FN' => 'Fernando de Noronha',
		'GO' => 'Goias',
		'MA' => 'Maranhao',
		'MG' => 'Minas Gerais',
		'MS' => 'Mato Grosso do Sul',
		'MT' => 'Mato Grosso',
		'PA' => 'Para',
		'PB' => 'Paraiba',
		'PE' => 'Pernambuco',
		'PI' => 'Piaui',
		'PR' => 'Parana',
		'RJ' => 'Rio de Janeiro',
		'RN' => 'Rio Grande do Norte',
		'RO' => 'Rondonia',
		'RR' => 'Roraima',
		'RS' => 'Rio Grande do Sul',
		'SC' => 'Santa Catarina',
		'SE' => 'Sergipe',
		'SP' => 'Sao Paulo',
		'TO' => 'Tocatins'

	)
	);
	
	public function __construct() {
		$this->stateProvinces['GB'] = $this->stateProvinces['UK'];
	}
	public function getStateProvinceName($v, $country) {
		foreach($this->stateProvinces as $countryKey => $countries) {
			foreach($countries as $key => $value) {
				if ($key == $v && $country == $countryKey) {
					return $value;
				}
			}
		}
	}
	
	public function getStateProvinceArray($k) {
		$a = $this->stateProvinces[$k];
		asort($a);
		return $a;
	}
	
	public function getAll() {
		$sp = $this->stateProvinces;
		foreach($sp as $p => $pv) {
			asort($sp[$p]);
		}
		return $sp;
	}
	
	/** 
	 * Returns an array of US states
	 * @deprecated
	 * @return array
	 */
	public function getStates() {
		return $this->stateProvinces['US'];
	}
	
	/** 
	 * Returns an array of Canadian provinces
	 * @deprecated
	 * @return array
	 */
	public function getCanadianProvinces() {
		return $this->stateProvinces['CA'];
	}
	

}

?>