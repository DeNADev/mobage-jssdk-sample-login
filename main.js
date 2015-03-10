/**
 * The MIT License (MIT)
 * Copyright (c) 2014-2015 DeNA Co., Ltd.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

(function() {
    var mobageLoginButton,
        mobageLogoutButton,
        clientData = {}
    ;

    document.addEventListener('DOMContentLoaded', function() {
        mobageLoginButton  = document.getElementById('mobage-login');
        mobageLogoutButton = document.getElementById('mobage-logout');

        // logout procedure
        mobageLogoutButton.addEventListener('click', function() {
            mobage.oauth.logout({}, function(error, result) {
                if (result) {
                    mobageLogoutButton.style.cssText = 'visibility:hidden';
                    mobageLoginButton.style.cssText  = 'visibility:visible';

                    var myInfo = document.getElementById('my-mobage-info');
                    if ("innerText" in myInfo) {
                        myInfo.innerText = '';
                    } else {
                        myInfo.textContent = '';
                    }
                    location.href = '../others/logout.php';
                }
            });
        });
    });

    document.addEventListener('mobageReady', function() {
        var html = document.getElementsByTagName('html')[0];

        clientData = {
            clientId     : html.dataset.clientId,
            redirectUri  : html.dataset.redirectUri,
            logoutUri    : html.dataset.logoutUri,
            state        : html.dataset.state,
            sessionState : html.dataset.sessionState
        };

        mobage.init({ clientId: clientData.clientId, redirectUri: clientData.redirectUri });

        if (clientData.sessionState) {
            mobageLoginButton.style.cssText = 'visibility:hidden';
            mobageLogoutButton.style.cssText = 'visibility:visible';
            mobage.event.subscribe('oauth.sessionStateChange', clientData.sessionState, function(result) {
                if (result === 'changed') {
                    location.href = clientData.logoutUri;
                }   
            });
            console.log('sessionState is OK');
            getMyInformation();

        } else if (clientData.state) {
            var params = { state: clientData.state};
            mobage.oauth.getConnectedStatus(params, function(err, result) {
                if (result) {
                    console.log('getConnectedStatus is OK');
                    sendToRedirectURI(result);
                    getMyInformation();
                } else {
                    console.log('getConnectedStatus failed');
                    mobageLoginButton.style.cssText  = 'visibility:visible';
                    mobageLogoutButton.style.cssText = 'visibility:hidden'; 
                }
            });

            mobageLoginButton.addEventListener('click', function() {
                mobage.oauth.connect({ state: clientData.state }, function(err, result) {
                    if (err) {
                        console.log('login failed: error = ', err);
                    } else {
                       // console.log('result:', result);
                        console.log('mobage.oauth.connect is OK(login success)');
                        sendToRedirectURI(result);
                        getMyInformation();
                    }
            
                });
            });
        }
        console.log('mobageReady is OK');
    });


    function sendToRedirectURI(result) {
        mobageLoginButton.style.cssText = 'visibility:hidden';
        mobageLogoutButton.style.cssText = 'visibility:visible';

        var response = result.response;
        var payload  = {
            code          : response.code,
            state         : response.state,
            session_state : response.session_state
        };

        var req = new XMLHttpRequest();
        req.open('POST', clientData.redirectUri);
        req.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
        req.addEventListener('load', function() {
            // ログイン処理後のハンドリング
        } , false);
        req.send(JSON.stringify(payload));
    }

    function getMyInformation () {
        mobage.api.people.get(
            { userId: '@me', groupId: '@self' },
            function(err, people) {
                var myInfo = document.getElementById('my-mobage-info');
                if ("innerText" in myInfo) {
                    myInfo.innerText = 'Hello, ' + people.nickname;
                } else {
                    myInfo.textContent = 'Hello, ' + people.nickname;
                }
            }
        );
    }

})(this.self || global);
