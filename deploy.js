let child_process = require('child_process');
let fs = require('fs');

let target = 'wpcarelab.com';
let choice = (process.argv.length > 2) ? process.argv[2] : 'root';
let auth = (process.argv.length > 3) ? true : false;
let pwd = process.env['BAMPWD'];
let deployAll = (choice == 'full');

function exec(cmd, opts = { stdio: 'inherit' }) {
    try {
        //console.log(cmd);
        child_process.execSync(cmd, opts);
    } catch (e) {
        console.log(e);
    }
}

if (choice == "help") {
    console.log(`node deploy.js [full|theme|views|root|<path>] [auth]`);
    process.exit(0);
}

if (auth) {
    exec('ssh-agent');
    exec('ssh-add');
}

choice = choice.replace(/\\/g, '\\');
if (fs.existsSync(`C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\wpcarelab\\${choice}`)) {
    let remoteFilePath = choice.replace(/\\/g, '/');
    return exec(`scp -P 7822 C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\wpcarelab\\${choice} bamarger@bamargera.com:${target}/wp-content/themes/wpcarelab/${remoteFilePath}`)
} else {
    console.log(`file doesn't exist C:\\xampp\\htdocs\\wpcarelab\\themes\\wpcarelab\\${choice}`);
}

/*if (deployAll || choice == "vendor") {
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\astra-child\\vendor bamarger@bamargera.com:${target}/wp-content/themes/astra-child`);
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\vendor bamarger@bamargera.com:${target}/wp-content`);
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\mu-plugins bamarger@bamargera.com:${target}/wp-content/`);
}

if (deployAll || choice == 'plugins') {
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\plugins\\front-end-pm bamarger@bamargera.com:${target}/wp-content/plugins/`);
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\plugins\\stripe-payments bamarger@bamargera.com:${target}/wp-content/plugins/`);
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\plugins\\stripe-payments-subscriptions bamarger@bamargera.com:${target}/wp-content/plugins/`);
}*/

/*if (deployAll || choice == 'theme' || choice == 'views') {
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\astra-child\\views bamarger@bamargera.com:${target}/wp-content/themes/astra-child`);
}*/

if (deployAll || choice == 'theme' || choice == 'root') {
    exec(`scp -P 7822 C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\wpcarelab\\* bamarger@bamargera.com:${target}/wp-content/themes/wpcarelab`);
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\wpcarelab\\inc bamarger@bamargera.com:${target}/wp-content/themes/wpcarelab`);
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\wpcarelab\\woocommerce bamarger@bamargera.com:${target}/wp-content/themes/wpcarelab`);
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\wpcarelab\\template-parts bamarger@bamargera.com:${target}/wp-content/themes/wpcarelab`);
}

/*if (deployAll || choice == 'theme') {
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\astra-child\\assets\\images bamarger@bamargera.com:${target}/wp-content/themes/astra-child/assets`);
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\astra-child\\assets\\js bamarger@bamargera.com:${target}/wp-content/themes/astra-child/assets`);
    exec(`scp -P 7822 C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\astra-child\\assets\\stylesheets bamarger@bamargera.com:${target}/wp-content/themes/astra-child/assets`);
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\astra-child\\Controllers bamarger@bamargera.com:${target}/wp-content/themes/astra-child`);
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\astra-child\\Databases bamarger@bamargera.com:${target}/wp-content/themes/astra-child`);
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\astra-child\\models bamarger@bamargera.com:${target}/wp-content/themes/astra-child`);
    exec(`scp -P 7822 -r C:\\xampp\\htdocs\\wpcarelab\\wp-content\\themes\\astra-child\\includes bamarger@bamargera.com:${target}/wp-content/themes/astra-child`);

    //Commands to run migrations on remote server with plink
    exec(`echo cd ~/${target}/wp-content; vendor/bin/wp dbi migrate --setup; vendor/bin/wp dbi migrate > .runmigrations.sh`);
    exec(`plink -pw ${pwd} -P 7822 bamargera -m .runmigrations.sh -batch`);
    exec(`del .runmigrations.sh`);
}*/
