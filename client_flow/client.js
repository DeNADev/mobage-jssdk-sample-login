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
                    removeSession();
                    showLoginButton();

                    var myInfo = document.getElementById('my-mobage-info');
                    if ("innerText" in myInfo) {
                        myInfo.innerText = '';
                    } else {
                        myInfo.textContent = '';
                    }
                }
            });
        });
    });

    document.addEventListener('mobageReady', function() {
        var html  = document.getElementsByTagName('html')[0];
        
        var state, session_state;
        state = generateState();
        if (isLoggedIn ()) {
            var session = readSession();
            session_state = session.session_state;
        }

        clientData = {
            clientId     : html.dataset.clientId,
            redirectUri  : html.dataset.redirectUri,
            state        : state,
            session_state: session_state
        };

        mobage.init({ clientId: clientData.clientId, redirectUri: clientData.redirectUri });

        if (clientData.state) {
            var params = { state: clientData.state};
            mobage.oauth.getConnectedStatus(params, function(err, result) {
                if (result) {
                    showLogoutButton();
                    getMyInformation();
                } else {
                    showLoginButton();
                }
            });

            mobageLoginButton.addEventListener('click', function() {
                mobage.oauth.connect({ state: clientData.state }, function(err, result) {
                    if (err) {
                        console.log('login failed: error = ', err);
                    } else {
                        console.log('result:', result);
                        console.log('mobage.oauth.connect is OK(login success)');
                        writeSession(result);
                        getMyInformation();
                        showLogoutButton();
                    }
            
                });
            });
        }
        console.log('mobageReady is OK');

    });

    function generateState() {
        // Transfer Random number to Strings(0-9a-z), and slice 16 words from backward.
        var state = 'mobage-connect_' + Math.random().toString(36).slice(-16);
        return state;
    }

    function readSession () {
        var session = JSON.parse(window.sessionStorage.getItem('session'));
        return session;
    }

    function writeSession(result) {
        var session  = {
            session_state : result.response.session_state
        };
        window.sessionStorage.setItem("session", JSON.stringify(session));
    }

    function removeSession() {
        window.sessionStorage.removeItem('session');
    }

    function isLoggedIn () {
        var session = readSession();
        return session ? true : false;
    }

    function showLoginButton () {
        mobageLoginButton.style.cssText  = 'visibility:visible';
        mobageLogoutButton.style.cssText = 'visibility:hidden';
    }

    function showLogoutButton () {
        mobageLoginButton.style.cssText  = 'visibility:hidden';
        mobageLogoutButton.style.cssText = 'visibility:visible';
    }

    function getMyInformation () {
        mobage.api.people.get(
            { userId: '@me', groupId: '@self' },
            function(err, people) {
                if (err) {
                    console.log(err);
                }
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
