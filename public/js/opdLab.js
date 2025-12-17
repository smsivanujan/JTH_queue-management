const testLabels = {
    'Urine Test': { en: 'Urine Test', ta: 'சிறுநீர் பரிசோதனை', si: 'මූත්‍ර පරීක්ෂණය', color: 'white' },
    'Full Blood Count': { en: 'FBC', ta: 'குருதி கல எண்ணிக்கை பரிசோதனை', si: 'රුධිර සෙලුල ගණන පරීක්ෂණය', color: 'green' },
    'ESR': { en: 'ESR', ta: 'செங்குருதி கல அடைவு பரிசோதனை', si: 'රතු රුධිර සෙලුල ප්‍රතිසංස්කරණ පරීක්ෂණය', color: 'red' }
};

let secondScreen = null;

document.getElementById('callBtn').addEventListener('click', () => {
    const testSelect = document.getElementById('testSelect');
    const testLabel = testSelect.value;
    if (!testLabel) return alert("Please select a test");

    const start = parseInt(document.getElementById('text1').value);
    const end = parseInt(document.getElementById('text2').value);
    if (isNaN(start) || isNaN(end) || start > end) return alert("Enter valid start/end numbers");

    const labelInfo = testLabels[testLabel];
    displayTokens(start, end, labelInfo);
});

document.getElementById('openSecondScreen').addEventListener('click', () => {
    if (!secondScreen || secondScreen.closed) {
        secondScreen = window.open("{{ route('opd.lab.second-screen') }}", "secondScreen", `width=${screen.availWidth},height=${screen.availHeight}`);
        localStorage.setItem('secondScreen', secondScreen.name);
    }
});

function displayTokens(start, end, labelInfo) {
    const tokenDisplay = document.getElementById('tokenDisplay');
    const testLabelDiv = document.getElementById('testLabel');
    tokenDisplay.innerHTML = '';
    testLabelDiv.textContent = `${labelInfo.en} / ${labelInfo.ta} / ${labelInfo.si}`;
    testLabelDiv.style.color = labelInfo.color;

    for (let i = start; i <= end; i++) {
        const div = document.createElement('div');
        div.className = 'token';
        div.textContent = i;
        div.style.backgroundColor = labelInfo.color;
        tokenDisplay.appendChild(div);
    }

    if (secondScreen && !secondScreen.closed) {
        const secDoc = secondScreen.document;
        const secLabel = secDoc.getElementById('secondTestLabel');
        const secToken = secDoc.getElementById('secondTokenDisplay');
        if (secLabel && secToken) {
            secLabel.textContent = `${labelInfo.en} / ${labelInfo.ta} / ${labelInfo.si}`;
            secLabel.style.color = labelInfo.color;
            secToken.innerHTML = '';
            for (let i = start; i <= end; i++) {
                const div = secDoc.createElement('div');
                div.className = 'token';
                div.textContent = i;
                div.style.backgroundColor = labelInfo.color;
                secToken.appendChild(div);
            }
        }
    }

    speakText(`${labelInfo.ta} பரிசோதனை எண் ${start} முதல் ${end} வரை வரவும்`);
}

function speakText(text) {
    const msg = new SpeechSynthesisUtterance(text);
    msg.lang = 'ta-IN';
    msg.rate = 0.9;
    window.speechSynthesis.cancel();
    window.speechSynthesis.speak(msg);
}
