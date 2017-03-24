(function(_0xb032x1, _0xb032x2) {
    var _0xb032x3, _0xb032x4 = 0,
        _0xb032x5 = {},
        _0xb032x6 = {},
        _0xb032x7 = Array['prototype']['slice'],
        _0xb032x8 = function(_0xb032x2) {
            return _0xb032x1['isArray'](_0xb032x2) ? _0xb032x2 : [_0xb032x2]
        },
        _0xb032x9 = 'id',
        _0xb032xa = 'form',
        _0xb032xb = 'click',
        _0xb032xc = 'submit',
        _0xb032xd = 'disabled',
        _0xb032xe = 'wizard',
        _0xb032xf = 'default',
        _0xb032x10 = 'number',
        _0xb032x11 = 'object',
        _0xb032x12 = 'string',
        _0xb032x13 = 'boolean',
        _0xb032x14 = 'afterBackward',
        _0xb032x15 = 'afterDestroy',
        _0xb032x16 = 'afterForward',
        _0xb032x17 = 'afterSelect',
        _0xb032x18 = 'beforeBackward',
        _0xb032x19 = 'beforeDestroy',
        _0xb032x1a = 'beforeForward',
        _0xb032x1b = 'beforeSelect',
        _0xb032x1c = 'beforeSubmit';
    _0xb032x1['each']('branch form header step wrapper' ['split'](' '), function() {
        _0xb032x5[this] = '.' + (_0xb032x6[this] = _0xb032xe + '-' + this)
    });
    _0xb032x1['widget']('kf.' + _0xb032xe, {
        version: '1.0.0',
        options: {
            animations: {
                show: {
                    options: {
                        duration: 0
                    },
                    properties: {
                        opacity: 'show'
                    }
                },
                hide: {
                    options: {
                        duration: 0
                    },
                    properties: {
                        opacity: 'hide'
                    }
                }
            },
            backward: '.backward',
            branches: '.branch',
            disabled: false,
            enableSubmit: false,
            forward: '.forward',
            header: ':header:first',
            initialStep: 0,
            stateAttribute: 'data-state',
            stepClasses: {
                current: 'current',
                exclude: 'exclude',
                stop: 'stop',
                submit: 'submit',
                unidirectional: 'unidirectional'
            },
            steps: '.step',
            submit: ':submit',
            transitions: {},
            unidirectional: false,
            afterBackward: null,
            afterDestroy: null,
            afterForward: null,
            afterSelect: null,
            beforeBackward: null,
            beforeDestroy: null,
            beforeForward: null,
            beforeSelect: null,
            create: null
        },
        _create: function() {
            var _0xb032x2, _0xb032x3, _0xb032x7 = this,
                _0xb032xb = _0xb032x7['options'],
                _0xb032xc = _0xb032x7['element'],
                _0xb032xd = _0xb032xc['find'](_0xb032xb['steps']),
                _0xb032x10 = _0xb032xd['eq'](0)['parent']();
            if (_0xb032xc[0]['elements']) {
                _0xb032x2 = _0xb032xc
            }
            else {
                if (!(_0xb032x2 = _0xb032xc['find'](_0xb032xa))['length']) {
                    _0xb032x2 = _0xb032xc['closest'](_0xb032xa)
                }
            };
            if (!(_0xb032x3 = _0xb032xc['find'](_0xb032xb['header']))['length']) {
                _0xb032x3 = _0xb032x2['find'](_0xb032xb['header'])
            };
            _0xb032x7['elements'] = {
                form: _0xb032x2['addClass'](_0xb032x6['form']),
                submit: _0xb032x2['find'](_0xb032xb['submit']),
                forward: _0xb032x2['find'](_0xb032xb['forward']),
                backward: _0xb032x2['find'](_0xb032xb['backward']),
                header: _0xb032x3['addClass'](_0xb032x6['header']),
                steps: _0xb032xc['find'](_0xb032xb['steps'])['hide']()['addClass'](_0xb032x6['step']),
                branches: _0xb032xc['find'](_0xb032xb['branches'])['add'](_0xb032x10)['addClass'](
                    _0xb032x6['branch']),
                stepsWrapper: _0xb032x10['addClass'](_0xb032x6['wrapper']),
                wizard: _0xb032xc['addClass'](_0xb032xe)
            };
            if (!_0xb032x10['attr'](_0xb032x9)) {
                _0xb032x10['attr'](_0xb032x9, _0xb032xe + '-' + ++_0xb032x4)
            };
            _0xb032x7['elements']['forward']['click'](function(_0xb032x1) {
                _0xb032x1['preventDefault']();
                _0xb032x7['forward'](_0xb032x1)
            });
            _0xb032x7['elements']['backward']['click'](function(_0xb032x1) {
                _0xb032x1['preventDefault']();
                _0xb032x7['backward'](_0xb032x1)
            });
            _0xb032x7['_currentState'] = {
                branchesActivated: [],
                stepsActivated: []
            };
            _0xb032x7['_stepCount'] = _0xb032x7['elements']['steps']['length'];
            _0xb032x7['_lastStepIndex'] = _0xb032x7['_stepCount'] - 1;
            _0xb032x7['_branchLabels'] = [];
            _0xb032x7['elements']['steps']['each'](function(_0xb032x2) {
                _0xb032x7['_branchLabels'][_0xb032x2] = _0xb032x1(this)['parent']()['attr'](
                    _0xb032x9)
            });
            _0xb032x7['_excludesFilter'] = function() {
                return !_0xb032x1(this)['hasClass'](_0xb032xb['stepClasses']['exclude'])
            };
            if (!_0xb032xb['transitions'][_0xb032xf]) {
                _0xb032xb['transitions'][_0xb032xf] = function(_0xb032x1) {
                    return _0xb032x7['stepIndex'](_0xb032x1['nextAll'](_0xb032x5['step']))
                }
            };
            _0xb032x7['select']['apply'](_0xb032x7, _0xb032x8(_0xb032xb['initialStep']))
        },
        _fastForward: function(_0xb032x3, _0xb032x4, _0xb032x5) {
            var _0xb032x6 = 0,
                _0xb032x7 = this,
                _0xb032x8 = _0xb032x7['_currentState']['stepIndex'],
                _0xb032x9 = [_0xb032x8];
            if (_0xb032x1['isFunction'](_0xb032x4)) {
                _0xb032x5 = _0xb032x4;
                _0xb032x4 = _0xb032x2
            };
            (function _0xb032xa() {
                _0xb032x7._transition(_0xb032x8, function(_0xb032x2, _0xb032xb) {
                    if ((_0xb032x8 = _0xb032x7['stepIndex'](_0xb032x2, _0xb032xb)) === -1) {
                        throw new Error('[_fastForward]: Invalid step "' + _0xb032x2 + '"')
                    }
                    else {
                        if (_0xb032x1['inArray'](_0xb032x8, _0xb032x9) >= 0) {
                            throw new Error('[_fastForward]: Recursion detected on step "' +
                                _0xb032x2 + '"')
                        }
                        else {
                            _0xb032x9['push'](_0xb032x8);
                            if (_0xb032x8 === _0xb032x7['_lastStepIndex'] || (_0xb032x4 ?
                                    ++_0xb032x6 : _0xb032x8) === _0xb032x3) {
                                _0xb032x5['call'](_0xb032x7, _0xb032x8, _0xb032x9)
                            }
                            else {
                                _0xb032xa()
                            }
                        }
                    }
                })
            })()
        },
        _find: function(_0xb032x2, _0xb032x3, _0xb032x4) {
            function _0xb032xd(_0xb032x1, _0xb032x2) {
                if (_0xb032x2 === _0xb032x9) {
                    _0xb032x5 = _0xb032x2;
                    return false
                }
            }
            var _0xb032x5, _0xb032x6, _0xb032x7, _0xb032x9, _0xb032xa, _0xb032xb = [],
                _0xb032xc = _0xb032x3 instanceof jQuery ? _0xb032x3 : _0xb032x1(_0xb032x3);
            if (_0xb032x2 !== null && _0xb032xc['length']) {
                _0xb032x2 = _0xb032x8(_0xb032x2);
                for (_0xb032x6 = 0, _0xb032x7 = _0xb032x2['length']; _0xb032x6 < _0xb032x7; _0xb032x6++) {
                    _0xb032x5 = null;
                    _0xb032x9 = _0xb032x2[_0xb032x6];
                    _0xb032xa = typeof _0xb032x9;
                    if (_0xb032xa === _0xb032x10) {
                        _0xb032x5 = _0xb032xc['get'](_0xb032x9)
                    }
                    else {
                        if (_0xb032xa === _0xb032x12) {
                            _0xb032x5 = document['getElementById'](_0xb032x9['replace']('#', ''))
                        }
                        else {
                            if (_0xb032xa === _0xb032x11) {
                                if (_0xb032x9 instanceof jQuery && _0xb032x9['length']) {
                                    _0xb032x9 = _0xb032x9[0]
                                };
                                if (_0xb032x9['nodeType']) {
                                    _0xb032xc['each'](_0xb032xd)
                                }
                            }
                        }
                    };
                    if (_0xb032x5) {
                        _0xb032xb['push'](_0xb032x5)
                    }
                }
            };
            return _0xb032x4 === false ? _0xb032xb : _0xb032x1(_0xb032xb)
        },
        _move: function(_0xb032x3, _0xb032x4, _0xb032x5, _0xb032x6, _0xb032x7) {
            function _0xb032xa(_0xb032x3, _0xb032x4) {
                _0xb032x7['call'](_0xb032x8, _0xb032x3, _0xb032x1['isArray'](_0xb032x6) ? _0xb032x6 :
                    _0xb032x6 !== false ? _0xb032x4 : _0xb032x2)
            }
            var _0xb032x8 = this,
                _0xb032x9 = _0xb032x8['_currentState'];
            if (typeof _0xb032x4 === _0xb032x13) {
                _0xb032x7 = _0xb032x6;
                _0xb032x6 = _0xb032x5;
                _0xb032x5 = _0xb032x4;
                _0xb032x4 = _0xb032x2
            };
            if (_0xb032x5 === true) {
                if (_0xb032x3 > 0) {
                    _0xb032x8._fastForward(_0xb032x3, _0xb032x5, _0xb032xa)
                }
                else {
                    _0xb032x7['call'](_0xb032x8, _0xb032x9['stepsActivated'][Math['max'](0, _0xb032x3 +
                        (_0xb032x9['stepsActivated']['length'] - 1))])
                }
            }
            else {
                if ((_0xb032x3 = _0xb032x8['stepIndex'](_0xb032x3, _0xb032x4)) !== -1) {
                    if (_0xb032x3 > _0xb032x9['stepIndex']) {
                        _0xb032x8._fastForward(_0xb032x3, _0xb032xa)
                    }
                    else {
                        _0xb032xa['call'](_0xb032x8, _0xb032x3)
                    }
                }
            }
        },
        _state: function(_0xb032x2, _0xb032x3) {
            if (!this['isValidStepIndex'](_0xb032x2)) {
                return null
            };
            var _0xb032x4 = this['options'],
                _0xb032x6 = _0xb032x1['extend'](true, {}, this._currentState);
            _0xb032x3 = _0xb032x8(_0xb032x3 || _0xb032x2);
            _0xb032x6['step'] = this['elements']['steps']['eq'](_0xb032x2);
            _0xb032x6['branch'] = _0xb032x6['step']['parent']();
            _0xb032x6['branchStepCount'] = _0xb032x6['branch']['children'](_0xb032x5['step'])['length'];
            _0xb032x6['isMovingForward'] = _0xb032x2 > _0xb032x6['stepIndex'];
            _0xb032x6['stepIndexInBranch'] = _0xb032x6['branch']['children'](_0xb032x5['step'])['index']
                (_0xb032x6['step']);
            var _0xb032x7, _0xb032x9, _0xb032xa, _0xb032xb = 0,
                _0xb032xc = _0xb032x3['length'];
            for (; _0xb032xb < _0xb032xc; _0xb032xb++) {
                _0xb032x2 = _0xb032x3[_0xb032xb];
                _0xb032x7 = this['_branchLabels'][_0xb032x2];
                if (!_0xb032x6['stepIndex'] || _0xb032x6['stepIndex'] < _0xb032x2) {
                    if (_0xb032x1['inArray'](_0xb032x2, _0xb032x6['stepsActivated']) < 0) {
                        _0xb032x6['stepsActivated']['push'](_0xb032x2);
                        if (_0xb032x1['inArray'](_0xb032x7, _0xb032x6['branchesActivated']) < 0) {
                            _0xb032x6['branchesActivated']['push'](_0xb032x7)
                        }
                    }
                }
                else {
                    if (_0xb032x6['stepIndex'] > _0xb032x2) {
                        _0xb032x9 = _0xb032x1['inArray'](_0xb032x7, _0xb032x6['branchesActivated']) + 1;
                        _0xb032xa = _0xb032x1['inArray'](_0xb032x2, _0xb032x6['stepsActivated']) + 1;
                        if (_0xb032x9 > 0) {
                            _0xb032x6['branchesActivated']['splice'](_0xb032x9, _0xb032x6[
                                'branchesActivated']['length'] - 1)
                        };
                        if (_0xb032xa > 0) {
                            _0xb032x6['stepsActivated']['splice'](_0xb032xa, _0xb032x6['stepsActivated']
                                ['length'] - 1)
                        }
                    }
                };
                _0xb032x6['stepIndex'] = _0xb032x2;
                _0xb032x6['branchLabel'] = _0xb032x7
            };
            _0xb032x6['stepsComplete'] = Math['max'](0, this._find(_0xb032x6['stepsActivated'], this[
                'elements']['steps'])['filter'](this._excludesFilter)['length'] - 1);
            _0xb032x6['stepsPossible'] = Math['max'](0, this._find(_0xb032x6['branchesActivated'], this[
                'elements']['branches'])['children'](_0xb032x5['step'])['filter'](this._excludesFilter)[
                'length'] - 1);
            _0xb032x1['extend'](_0xb032x6, {
                branchLabel: this['_branchLabels'][_0xb032x2],
                isFirstStep: _0xb032x2 === 0,
                isFirstStepInBranch: _0xb032x6['stepIndexInBranch'] === 0,
                isLastStep: _0xb032x2 === this['_lastStepIndex'],
                isLastStepInBranch: _0xb032x6['stepIndexInBranch'] === _0xb032x6[
                    'branchStepCount'] - 1,
                percentComplete: 100 * _0xb032x6['stepsComplete'] / _0xb032x6['stepsPossible'],
                stepsRemaining: _0xb032x6['stepsPossible'] - _0xb032x6['stepsComplete']
            });
            return _0xb032x6
        },
        _transition: function(_0xb032x3, _0xb032x4, _0xb032x5) {
            var _0xb032x6 = this;
            if (_0xb032x1['isFunction'](_0xb032x3)) {
                _0xb032x5 = _0xb032x3;
                _0xb032x3 = _0xb032x6['_currentState']['stepIndex'];
                _0xb032x4 = _0xb032x2
            }
            else {
                if (_0xb032x1['isFunction'](_0xb032x4)) {
                    _0xb032x5 = _0xb032x4;
                    _0xb032x4 = _0xb032x2
                }
            };
            var _0xb032x9, _0xb032xa = _0xb032x6['options'],
                _0xb032xb = _0xb032x6['step'](_0xb032x3, _0xb032x4),
                _0xb032xc = _0xb032xb['attr'](_0xb032xa['stateAttribute']),
                _0xb032xd = _0xb032xc ? _0xb032xa['transitions'][_0xb032xc] : _0xb032xa['transitions'][
                    _0xb032xf];
            if (_0xb032x1['isFunction'](_0xb032xd)) {
                _0xb032x9 = _0xb032xd['call'](_0xb032x6, _0xb032xb, function() {
                    return _0xb032x5['apply'](_0xb032x6, _0xb032x7['call'](arguments))
                })
            }
            else {
                _0xb032x9 = _0xb032xc
            };
            if (_0xb032x9 !== _0xb032x2 && _0xb032x9 !== false) {
                _0xb032x5['apply'](_0xb032x6, _0xb032x8(_0xb032x9))
            };
            return _0xb032x9
        },
        _update: function(_0xb032x2, _0xb032x3) {
            var _0xb032x4 = this['_currentState'],
                _0xb032x5 = this['options'];
            if (_0xb032x4['step']) {
                if (_0xb032x5['disabled'] || !_0xb032x3 || _0xb032x3['stepIndex'] === _0xb032x4[
                        'stepIndex'] || !this._trigger(_0xb032x1b, _0xb032x2, _0xb032x3) || _0xb032x3[
                        'isMovingForward'] && !this._trigger(_0xb032x1a, _0xb032x2, _0xb032x3) || !
                    _0xb032x3['isMovingForward'] && !this._trigger(_0xb032x18, _0xb032x2, _0xb032x3)) {
                    return
                };
                _0xb032x4['step']['removeClass'](_0xb032x5['stepClasses']['current'])['animate'](
                    _0xb032x5['animations']['hide']['properties'], _0xb032x1['extend']({},
                        _0xb032x5['animations']['hide']['options']))
            };
            this['_currentState'] = _0xb032x3;
            _0xb032x3['step']['addClass'](_0xb032x5['stepClasses']['current'])['animate'](_0xb032x5[
                'animations']['show']['properties'], _0xb032x1['extend']({}, _0xb032x5[
                'animations']['show']['options']));
            if (_0xb032x3['isFirstStep'] || _0xb032x5['unidirectional'] || _0xb032x3['step']['hasClass']
                (_0xb032x5['stepClasses']['unidirectional'])) {
                this['elements']['backward']['attr'](_0xb032xd, true)
            }
            else {
                this['elements']['backward']['removeAttr'](_0xb032xd)
            };
            if (_0xb032x3['isLastStepInBranch'] && !_0xb032x3['step']['attr'](_0xb032x5[
                    'stateAttribute']) || _0xb032x3['step']['hasClass'](_0xb032x5['stepClasses']['stop'])) {
                this['elements']['forward']['attr'](_0xb032xd, true)
            }
            else {
                this['elements']['forward']['removeAttr'](_0xb032xd)
            };
            if (_0xb032x5['enableSubmit'] || _0xb032x3['step']['hasClass'](_0xb032x5['stepClasses'][
                    'submit'])) {
                this['elements']['submit']['removeAttr'](_0xb032xd)
            }
            else {
                this['elements']['submit']['attr'](_0xb032xd, true)
            };
            if (_0xb032x4['step']) {
                this._trigger(_0xb032x17, _0xb032x2, _0xb032x3);
                this._trigger(_0xb032x3['isMovingForward'] ? _0xb032x16 : _0xb032x14, _0xb032x2,
                    _0xb032x3)
            }
        },
        backward: function(_0xb032x1, _0xb032x3) {
            if (typeof _0xb032x1 === _0xb032x10) {
                _0xb032x3 = _0xb032x1;
                _0xb032x1 = _0xb032x2
            };
            if (_0xb032x3 === _0xb032x2) {
                _0xb032x3 = 1
            };
            if (this['_currentState']['isFirstStep'] || typeof _0xb032x3 !== _0xb032x10) {
                return
            };
            this._move(-_0xb032x3, true, false, function(_0xb032x2, _0xb032x3) {
                this._update(_0xb032x1, this._state(_0xb032x2, _0xb032x3))
            })
        },
        branch: function(_0xb032x1) {
            return arguments['length'] ? this._find(_0xb032x1, this['elements']['branches']) : this[
                '_currentState']['branch']
        },
        branches: function(_0xb032x1) {
            return arguments['length'] ? this['branch'](_0xb032x1)['children'](_0xb032x5['branch']) :
                this['elements']['branches']
        },
        branchesActivated: function() {
            return this._find(this['_currentState']['branchesActivated'], this['elements']['branches'])
        },
        destroy: function() {
            var _0xb032x2 = this['elements'];
            if (!this._trigger(_0xb032x19, null, this['state']())) {
                return
            };
            this['element']['removeClass'](_0xb032xe);
            _0xb032x2['form']['removeClass'](_0xb032x6['form']);
            _0xb032x2['header']['removeClass'](_0xb032x6['header']);
            _0xb032x2['steps']['show']()['removeClass'](_0xb032x6['step']);
            _0xb032x2['stepsWrapper']['removeClass'](_0xb032x6['wrapper']);
            _0xb032x2['branches']['removeClass'](_0xb032x6['branch']);
            _0xb032x1['Widget']['prototype']['destroy']['call'](this);
            this._trigger(_0xb032x15)
        },
        form: function() {
            return this['elements']['form']
        },
        forward: function(_0xb032x1, _0xb032x3, _0xb032x4) {
            if (typeof _0xb032x1 === _0xb032x10) {
                _0xb032x4 = _0xb032x3;
                _0xb032x3 = _0xb032x1;
                _0xb032x1 = _0xb032x2
            };
            if (_0xb032x3 === _0xb032x2) {
                _0xb032x3 = 1
            };
            if (this['_currentState']['isLastStep'] || typeof _0xb032x3 !== _0xb032x10) {
                return
            };
            this._move(_0xb032x3, true, _0xb032x4, function(_0xb032x2, _0xb032x3) {
                this._update(_0xb032x1, this._state(_0xb032x2, _0xb032x3))
            })
        },
        isValidStep: function(_0xb032x1, _0xb032x2) {
            return this['isValidStepIndex'](this['stepIndex'](_0xb032x1, _0xb032x2))
        },
        isValidStepIndex: function(_0xb032x1) {
            return typeof _0xb032x1 === _0xb032x10 && _0xb032x1 >= 0 && _0xb032x1 <= this[
                '_lastStepIndex']
        },
        stepCount: function() {
            return this['_stepCount']
        },
        select: function(_0xb032x3, _0xb032x4, _0xb032x5, _0xb032x6, _0xb032x7) {
            if (!(_0xb032x3 instanceof _0xb032x1['Event'])) {
                _0xb032x7 = _0xb032x6;
                _0xb032x6 = _0xb032x5;
                _0xb032x5 = _0xb032x4;
                _0xb032x4 = _0xb032x3;
                _0xb032x3 = _0xb032x2
            };
            if (_0xb032x4 === _0xb032x2) {
                return
            };
            if (_0xb032x1['isArray'](_0xb032x4)) {
                _0xb032x7 = _0xb032x6;
                _0xb032x6 = _0xb032x5;
                _0xb032x5 = _0xb032x4[1];
                _0xb032x4 = _0xb032x4[0]
            }
            else {
                if (typeof _0xb032x5 === _0xb032x13) {
                    _0xb032x7 = _0xb032x6;
                    _0xb032x6 = _0xb032x5;
                    _0xb032x5 = _0xb032x2
                }
                else {
                    if (_0xb032x1['isArray'](_0xb032x5)) {
                        _0xb032x7 = _0xb032x5;
                        _0xb032x5 = _0xb032x2
                    }
                }
            };
            this._move(_0xb032x4, _0xb032x5, _0xb032x6, _0xb032x7, function(_0xb032x1, _0xb032x2) {
                this._update(_0xb032x3, this._state(_0xb032x1, _0xb032x2))
            })
        },
        state: function(_0xb032x3, _0xb032x4, _0xb032x5) {
            if (!arguments['length']) {
                return this['_currentState']
            };
            if (_0xb032x1['isArray'](_0xb032x3)) {
                _0xb032x5 = _0xb032x4;
                _0xb032x4 = _0xb032x3[1];
                _0xb032x3 = _0xb032x3[0]
            }
            else {
                if (_0xb032x1['isArray'](_0xb032x4)) {
                    _0xb032x5 = _0xb032x4;
                    _0xb032x4 = _0xb032x2
                }
            };
            return this._state(this['stepIndex'](_0xb032x3, _0xb032x4), _0xb032x5)
        },
        step: function(_0xb032x3, _0xb032x4) {
            if (!arguments['length']) {
                return this['_currentState']['step']
            };
            if (_0xb032x1['isArray'](_0xb032x3)) {
                _0xb032x4 = _0xb032x3[1];
                _0xb032x3 = _0xb032x3[0]
            };
            var _0xb032x5, _0xb032x7 = typeof _0xb032x3;
            if (_0xb032x7 === _0xb032x10) {
                _0xb032x5 = this._find(_0xb032x3, _0xb032x4 !== _0xb032x2 ? this['steps'](_0xb032x4) :
                    this['elements']['steps'])
            }
            else {
                _0xb032x5 = this._find(_0xb032x3, this['elements']['steps']['add'](this['elements'][
                    'branches']));
                if (_0xb032x5 && _0xb032x5['hasClass'](_0xb032x6['branch'])) {
                    _0xb032x5 = this._find(_0xb032x4 || 0, this['steps'](_0xb032x5))
                }
            };
            return _0xb032x5
        },
        stepIndex: function(_0xb032x3, _0xb032x4, _0xb032x6) {
            if (!arguments['length']) {
                return this['_currentState']['stepIndex']
            };
            var _0xb032x7;
            if (_0xb032x1['isArray'](_0xb032x3)) {
                _0xb032x6 = _0xb032x4;
                _0xb032x4 = _0xb032x3[1];
                _0xb032x3 = _0xb032x3[0]
            }
            else {
                if (typeof _0xb032x4 === _0xb032x13) {
                    _0xb032x6 = _0xb032x4;
                    _0xb032x4 = _0xb032x2
                }
            };
            return (_0xb032x7 = this['step'](_0xb032x3, _0xb032x4)) ? (_0xb032x6 ? _0xb032x7['siblings']
                    (_0xb032x5['step'])['andSelf']() : this['elements']['steps'])['index'](_0xb032x7) :
                -1
        },
        steps: function(_0xb032x1) {
            return arguments['length'] ? this['branch'](_0xb032x1)['children'](_0xb032x5['step']) :
                this['elements']['steps']
        },
        stepsActivated: function() {
            return this._find(this['_currentState']['stepsActivated'], this['elements']['steps'])
        },
        submit: function() {
            this['elements']['form']['submit']()
        }
    })
})(jQuery)

//# sourceMappingURL=jquery.wizard.js.map
