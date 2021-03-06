'use strict';
const axios = require("axios");

var postData = {
  from: "****SENDER*PHONE*ADDRESS******",
  to: ["***RECIPIENT*PHONE*ADDRESS*I.******"],
  body: "SZIVATTYU GEPHAZ: A NYOMAS MAGAS!!! (NYOMAS > 8.0 BAR)",
};

var postData2 = {
  from: "****SENDER*PHONE*ADDRESS******",
  to: ["***RECIPIENT*PHONE*ADDRESS*II.******"],
  body: "SZIVATTYU GEPHAZ: A NYOMAS MAGAS!!! (NYOMAS > 8.0 BAR)",
};


switch (process.argv[2]) {
	case '1':
		postData.body = "SZIVATTYU GEPHAZ: A NYOMAS MAGAS! (NYOMAS > 8.0 BAR)";
		break;
	case '2':
		postData.body = "SZIVATTYU GEPHAZ: A SZIVATTYU LEALLT MAGAS NYOMAS MIATT! (NYOMAS > 10 BAR)";
		break;
	case '3':
		postData.body = "SZIVATTYU GEPHAZ: A SZIVATTYU LEALLT, NEM UZEMEL!";
		break;
	case '4':
		postData.body = "SZIVATTYU GEPHAZ: A NYOMAS ALACSONY! (NYOMAS < 0.5 BAR)";
		break;
}
postData2.body = postData.body;


// console.log(postData);

/*
const fs = require('fs');
fs.writeFile('/home/csaba/nyof/proba.txt', postData.body, (err) => {
    // throws an error, you could also catch it here
    if (err) throw err;

    // success case, the file was saved
    console.log('Lyric saved!');
});
*/

const axiosConfig = {
  headers: {
    "Content-Type": "application/json",
    Authorization: "Bearer .....SINCH.API.BEARER TOKEN........",
  },
};

axios.post(
    "https://sms.api.sinch.com/xms/v1/....YOUR.SINCH.ID...../batches",
    postData,
    axiosConfig
  )
  .then((json) => {
    console.log(json);

    setTimeout(function () {
      axios.post(
        "https://sms.api.sinch.com/xms/v1/....YOUR.SINCH.ID...../batches",
        postData2,
        axiosConfig
      )
      .then((json2) => {
        console.log(json2);
      })
      .catch((error2) => {
        console.error(error2);
      });
    }, 5000);

  })
  .catch((error) => {
    console.error(error);

    setTimeout(function () {
      axios.post(
        "https://sms.api.sinch.com/xms/v1/....YOUR.SINCH.ID...../batches",
        postData2,
        axiosConfig
      )
      .then((json2) => {
        console.log(json2);
      })
      .catch((error2) => {
        console.error(error2);
      });
    }, 5000);

  });
