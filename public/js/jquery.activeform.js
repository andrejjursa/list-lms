(function($) {
    $.extend($.fn, {
        activeForm: function(options) {
            if (this.length === 0) {
                $.activeForm.errorMessage('Can\'t start conditional form on empty list of elements.');
                return this;
            }
            
            if ($(this[0]).is('form')) {
                var activeForm = $.data(this[0], 'activeFormObject');
                if (activeForm instanceof $.activeForm) {
                    return activeForm;
                }
                
                activeForm = new $.activeForm(options, this[0]);
                $.data(this[0], 'activeFormObject', activeForm);
                
                return activeForm;
            } else {
                $.activeForm.errorMessage('Can\'t assign conditional form to element of type ' + this[0].tagName + '.');
                return this;
            }
        },
        setActiveFormDisplayCondition: function(condition) {
            if (this.length === 0) {
                $.activeForm.errorMessage('Can\'t add condition to empty list, selector must match one DIV element.');
                return this;
            }
            
            if ($(this[0]).is($.activeForm.consts.formPartElements())) {
                var activeForm = $.activeForm.getActiveForm($.activeForm.getParentForm(this[0]));
                if (activeForm instanceof $.activeForm) {
                    activeForm.setDisplayCondition.call(activeForm, $(this[0]), condition);
                } else {
                    $.activeForm.errorMessage('Can\'t find activeForm on parent form element. (Or there is no parent form element?)');
                }
                return this;
            } else {
                $.activeForm.errorMessage('Can\'t add condition to element of type ' + this[0].tagName + '.');
                return this;
            }
        },
        addActiveFormPage: function(pageName, pageButtonElement, condition) {
            if (pageButtonElement === undefined) {
                pageButtonElement = null;
            }
            if (this.length === 0) {
                $.activeForm.errorMessage('Can\'t add page if list of selected elements is empty, selector must match one DIV of FIELDSET element.');
                return this;
            }
            
            if ($(this[0]).is($.activeForm.consts.pageElements())) {
                var activeForm = $.activeForm.getActiveForm($.activeForm.getParentForm(this[0]));
                if (activeForm instanceof $.activeForm) {
                    activeForm.addPageAndCondition.call(activeForm, $(this[0]), pageName, pageButtonElement, condition);
                } else {
                    $.activeForm.errorMessage('Can\'t find activeForm on parent form element. (Or there is no parent form element?)');
                }
                return this;
            } else {
                $.activeForm.errorMessage('Can\'t add page as element of type ' + this[0].tagName + '.');
                return this;
            }
        }
    });
    
    $.activeForm = function(options, formToProcess) {
        this.settings = $.extend(true, {}, $.activeForm.defaults, options);
        this.workOnForm = formToProcess;
        this.conditions = [];
        this.pages = [];
        this.currentPage = null;
        this.nextButton = null;
        this.prevButton = null;
        this.init();
    };
    
    $.extend($.activeForm, {
        defaults: {
            speed: 'fast',
            speedPageButton: 'normal',
            hiddenClass: '',
            onConditionCheck: null,
            nextPageSelector: null,
            previousPageSelector: null,
            changeButtonsTexts: false,
            prevPrefix: '&lt;&lt; ',
            prevSufix: '',
            nextPrefix: '',
            nextSufix: ' &gt;&gt;',
            pageButtonActiveClass: 'active',
            pageButtonInactiveClass: 'inactive',
            pageButtonVisitedClass: 'visited',
            pageButtonClickable: true,
            pageTransitionSpeed: 0
        },
        consts: {
            pageElements: function() { return 'div, fieldset, table, tr, tbody, thead, dl'; },
            formPartElements: function() { return 'div, tr, td, dl'; }
        },
        prototype: {
            init: function() {
                $(this.workOnForm).on('change', 'input, textarea, select, button', $.activeForm.processConditions);
                $(this.workOnForm).on('keyup', 'input, textarea, select, button', $.activeForm.processConditions);
                $(this.workOnForm).on('select', 'input, textarea, select, button', $.activeForm.processConditions);
                $(this.workOnForm).on('click', 'input, textarea, select, button', $.activeForm.processConditions);
                var actionForm = this;
                $(this.workOnForm).find(':reset').click(function(event){
                    event.preventDefault();
                    $(actionForm.workOnForm).each(function() {
                        this.reset();
                    });
                    if (actionForm.pages !== undefined && actionForm.pages.length > 0) {
                        actionForm.currentPage = 0;
                    }
                    $.activeForm.processConditions.call(this);
                });
                if (this.settings.nextPageSelector !== null) {
                    this.nextButton = $(this.workOnForm).find(this.settings.nextPageSelector);
                    $.data($(this.nextButton)[0], 'activeFormObject', this);
                    this.nextButton.click($.activeForm.nextButtonClick);
                }
                if (this.settings.previousPageSelector !== null) {
                    this.prevButton = $(this.workOnForm).find(this.settings.previousPageSelector);
                    $.data($(this.prevButton)[0], 'activeFormObject', this);
                    this.prevButton.click($.activeForm.prevButtonClick);
                }
            },
            setDisplayCondition: function(element, condition) {
                if (!(typeof condition === 'function')) {
                    $.activeForm.errorMessage('Condition must be function.');
                    return this;
                }
                if (typeof element === 'string') {
                    element = $(this.workOnForm).find(element);
                }
                if (element.length > 0 && $(element).is($.activeForm.consts.formPartElements())) {
                    var activeForm = $.activeForm.getActiveForm($.activeForm.getParentForm(element));
                    if (activeForm === this) {
                        var index = this.isConditionForElementSet(element);
                        if (index !== false) {
                            this.conditions[index].condition = condition;
                        } else {
                            var conditionObject = {
                                element: $(element)[0],
                                condition: condition
                            };
                            this.conditions.push(conditionObject);
                        }
                    } else {
                        $.activeForm.errorMessage('Selected DIV element do not belongs to this form.');
                    }
                } else {
                    $.activeForm.errorMessage('Only ' + $.activeForm.consts.formPartElements() + ' elements can be select for conditions.');
                }
                return this;
            },
            addPageAndCondition: function(element, pageName, pageButtonElement, condition) {
                if (pageButtonElement === undefined) { pageButtonElement = null; }
                if (condition !== undefined) {
                    if (!(typeof condition === 'function')) {
                        $.activeForm.errorMessage('Condition must be function.');
                        return this;
                    }
                }
                if (typeof element === 'string') {
                    element = $(this.workOnForm).find(element);
                }
                if (typeof pageButtonElement === 'string') {
                    pageButtonElement = $(this.workOnForm).find(pageButtonElement);
                }
                if (element.length > 0 && $(element).is($.activeForm.consts.pageElements())) {
                    var activeForm = $.activeForm.getActiveForm($.activeForm.getParentForm(element));
                    if (activeForm === this) {
                        var index = this.isPageSet(element);
                        if (index !== false) {
                            this.pages[index].condition = condition !== undefined ? condition : function() { return true; };
                            this.pages[index].pageName = pageName;
                            this.pages[index].pageButtonElement = pageButtonElement;
                        } else {
                            var pageObject = {
                                element: $(element)[0],
                                condition: condition !== undefined ? condition : function() { return true; },
                                pageName: pageName,
                                pageButtonElement: pageButtonElement
                            };
                            this.pages.push(pageObject);
                        }
                        if (pageButtonElement !== null) {
                            var currentPageIndex = this.isPageSet(element);
                            var activeForm = this;
                            $(pageButtonElement).click(function(event) {
                                event.preventDefault();
                                if (activeForm.settings.pageButtonClickable) {
                                    activeForm.selectPage(currentPageIndex);
                                }
                            });
                        }
                        this.currentPage = 0;
                    } else {
                        $.activeForm.errorMessage('Selected ' + $(element)[0].tagName + ' element do not belongs to this form.');
                    }
                } else {
                    $.activeForm.errorMessage('Only ' + $.activeForm.consts.pageElements() + ' elements can be selected as page.');
                }
                return this;
            },
            findElement: function(selector) {
                return $(this.workOnForm).find(selector);
            },
            renderPages: function() {
                
            },
            applyConditions: function() {
                var oldSpeed = this.settings.speedPageButton;
                this.settings.speedPageButton = 0;
                $.activeForm.showHideElements(this, 0);
                this.settings.speedPageButton = oldSpeed;
                if (typeof this.settings.onConditionCheck === 'function') {
                    this.settings.onConditionCheck.call(this);
                }
            },
            getConditionForElement: function(element) {
                var index = this.isConditionForElementSet(element);
                if (index !== false) {
                    return this.conditions[index].condition;
                }
                return function() { return true; };
            },
            isConditionForElementSet: function(element) {
                if (element.length > 0 && $(element).is($.activeForm.consts.formPartElements())) {
                    var elementDIV = $(element)[0];
                    for (index in this.conditions) {
                        if (this.conditions[index].element === elementDIV) {
                            return index;
                        }
                    }
                }
                return false; 
            },
            isPageSet: function(element) {
                if (element.length > 0 && $(element).is($.activeForm.consts.pageElements())) {
                    var elementPage = $(element)[0];
                    for (index in this.pages) {
                        if (this.pages[index].element === elementPage) {
                            return index;
                        }
                    }
                }
                return false;
            },
            getConditionForPage: function(element) {
                if (element.length > 0 && $(element).is($.activeForm.consts.pageElements())) {
                    var index = this.isPageSet(element);
                    if (index !== false) {
                        return this.pages[index].condition;
                    }
                }
                return function() { return true; };
            },
            isDisplayed: function(element) {
                if (typeof element === 'string') {
                    element = $(this.workOnForm).find(element);
                }
                if (element.length > 0 && $(element).is($.activeForm.consts.formPartElements())) {
                    var elementActiveForm = $.activeForm.getActiveForm($.activeForm.getParentForm(element));
                    if (elementActiveForm === this) {
                        var condition = this.getConditionForElement(element);
                        return condition.call(this);
                    }
                }
                return false;
            },
            idPageEnabled: function(element) {
                if (typeof element === 'string') {
                    element = $(this.workOnForm).find(element);
                }
                if (element.length > 0 && $(element).is($.activeForm.consts.pageElements())) {
                    var pageActiveForm = $.activeForm.getActiveForm($.activeForm.getParentForm(element));
                    if (pageActiveForm === this) {
                        var condition = this.getConditionForPage(element);
                        return condition.call(this);
                    }
                }
                return false;
            },
            getListOfVisiblePages: function() {
                var visiblePages = [];
                for (index in this.pages) {
                    if (this.pages[index].condition.call(this)) {
                        var activePageObject = {
                            index: index,
                            page: this.pages[index]
                        };
                        visiblePages.push(activePageObject);
                    }
                }
                return visiblePages;
            },
            getListOfHiddenPages: function() {
                var activePages = [];
                for (index in this.pages) {
                    if (!this.pages[index].condition.call(this)) {
                        var activePageObject = {
                            index: index,
                            page: this.pages[index]
                        };
                        activePages.push(activePageObject);
                    }
                }
                return activePages;
            },
            prevPage: function() {
                var visiblePages = this.getListOfVisiblePages();
                if (this.currentPage > 0) {
                    var newCurrentPage = this.currentPage;
                    do {
                        newCurrentPage--;
                    } while(newCurrentPage >= 0 && !$.activeForm.isIndexInPagesArray(newCurrentPage, visiblePages));
                    if (newCurrentPage >= 0) {
                        this.currentPage =  newCurrentPage;
                    }
                }
                $.activeForm.renderPages(this);
            },
            nextPage: function() {
                var visiblePages = this.getListOfVisiblePages();
                if (this.currentPage < this.pages.length - 1) {
                    var newCurrentPage = this.currentPage;
                    do {
                        newCurrentPage++;
                    } while (newCurrentPage < this.pages.length && !$.activeForm.isIndexInPagesArray(newCurrentPage, visiblePages));
                    if (newCurrentPage < this.pages.length) {
                        this.currentPage = newCurrentPage;
                    }
                }
                $.activeForm.renderPages(this);
            },
            selectPage: function(pageIndex) {
                if (this.pages.length > 0) {
                    if (pageIndex >= 0 && pageIndex < this.pages.length) {
                        this.currentPage = pageIndex;
                        $.activeForm.renderPages(this);
                    }
                }
            }
        },
        processConditions: function() {
            var activeForm = this;
            if (!(activeForm instanceof $.activeForm)) {
                if ($(this).is('input, select, textarea, button')) {
                    var formElement = $(this)[0].form; 
                    activeForm = $.activeForm.getActiveForm(formElement);                 
                } else {
                    return;
                }
            }
            $.activeForm.showHideElements(activeForm, activeForm.settings.speed);
            if (typeof activeForm.settings.onConditionCheck === 'function') {
                activeForm.settings.onConditionCheck.call(activeForm);
            }
        },
        showHideElements: function(activeForm, speed) {
            $.activeForm.showPages(activeForm);
            for (index in activeForm.conditions) {
                var condition = activeForm.conditions[index].condition;
                var element = activeForm.conditions[index].element;
                if (condition.call(activeForm)) {
                    $.activeForm.reenableInputElementsIn($(element), activeForm.settings.hiddenClass);
                } else {
                    $.activeForm.disableInputElementsIn($(element), activeForm.settings.hiddenClass);
                }
            }
            for (index in activeForm.conditions) {
                var condition = activeForm.conditions[index].condition;
                var element = activeForm.conditions[index].element;
                if (condition.call(activeForm)) {
                    $(element).show(speed);
                    $.activeForm.reenableInputElementsIn($(element), activeForm.settings.hiddenClass);
                } else {
                    $.activeForm.disableInputElementsIn($(element), activeForm.settings.hiddenClass);
                    $(element).hide(speed);
                }
            }
            $.activeForm.hidePages(activeForm);
            $.activeForm.renderPages(activeForm);
        },
        showPages: function(activeForm) {
            if (activeForm.pages.length > 0) {
                var visiblePages = activeForm.getListOfVisiblePages();
                for (index in visiblePages) {
                    var element = visiblePages[index].page.element;
                    $.activeForm.reenableInputElementsIn($(element), activeForm.settings.hiddenClass);
                    var pageButtonElement = visiblePages[index].page.pageButtonElement;
                    if (pageButtonElement !== null) {
                        $(pageButtonElement).fadeIn(activeForm.settings.speedPageButton);
                    }
                }
            }
        },
        hidePages: function(activeForm) {
            if (activeForm.pages.length > 0) {
                var hiddenPages = activeForm.getListOfHiddenPages();
                for (index in hiddenPages) {
                    var element = hiddenPages[index].page.element;
                    $.activeForm.disableInputElementsIn($(element), activeForm.settings.hiddenClass);
                    var pageButtonElement = hiddenPages[index].page.pageButtonElement;
                    if (pageButtonElement !== null) {
                        $(pageButtonElement).fadeOut(activeForm.settings.speedPageButton);
                    }
                }
            }
        },
        renderPages: function(activeForm) {
            if (activeForm.pages.length > 0) {
                var visiblePages = activeForm.getListOfVisiblePages();
                var hiddenPages = activeForm.getListOfHiddenPages();
                for(index in hiddenPages) {
                    var element = hiddenPages[index].page.element;
                    $(element).hide(activeForm.settings.pageTransitionSpeed);
                    var pageButtonElement = hiddenPages[index].page.pageButtonElement;
                    if (pageButtonElement !== null) {
                        $(pageButtonElement).removeClass(activeForm.settings.pageButtonActiveClass);
                        $(pageButtonElement).addClass(activeForm.settings.pageButtonInactiveClass);
                    }
                }
                while(activeForm.currentPage >= 0 && !$.activeForm.isIndexInPagesArray(activeForm.currentPage, visiblePages)) {
                    activeForm.currentPage--;
                }
                if(activeForm.currentPage < 0 && visiblePages.length > 0) {
                    this.currentPage = visiblePages[0].index;
                }
                var hasNext = false;
                var hasPrev = false;
                var nextTitle = '';
                var prevTitle = '';
                if (activeForm.currentPage >= 0) {
                    var pagesPrevNext = $.activeForm.getPrevAndNextPage(activeForm.currentPage, visiblePages);
                    if (pagesPrevNext.next !== null && activeForm.nextButton !== null) { hasNext = true; nextTitle = pagesPrevNext.next.pageName; }
                    if (pagesPrevNext.prev !== null && activeForm.prevButton !== null) { hasPrev = true; prevTitle = pagesPrevNext.prev.pageName; }
                }
                var setVisited = true;
                for (index in visiblePages) {
                    var pageButtonElement = visiblePages[index].page.pageButtonElement;
                    if (visiblePages[index].index === activeForm.currentPage) {
                        $(visiblePages[index].page.element).show(activeForm.settings.pageTransitionSpeed);
                        if (pageButtonElement !== null) {
                            $(pageButtonElement).removeClass(activeForm.settings.pageButtonInactiveClass);
                            $(pageButtonElement).addClass(activeForm.settings.pageButtonActiveClass);
                        }
                        setVisited = false;
                    } else {
                        $(visiblePages[index].page.element).hide(activeForm.settings.pageTransitionSpeed);
                        if (pageButtonElement !== null) {
                            $(pageButtonElement).removeClass(activeForm.settings.pageButtonActiveClass);
                            $(pageButtonElement).addClass(activeForm.settings.pageButtonInactiveClass);
                        }
                    }
                    if (setVisited) {
                        pageButtonElement.addClass(activeForm.settings.pageButtonVisitedClass);
                    } else {
                        pageButtonElement.removeClass(activeForm.settings.pageButtonVisitedClass);
                    }
                    if (activeForm.settings.pageButtonClickable) {
                        pageButtonElement.css('cursor', 'pointer');
                    } else {
                        pageButtonElement.css('cursor', '');
                    }
                }
                if (hasNext) {
                    activeForm.nextButton.attr('title', nextTitle);
                    if (activeForm.settings.changeButtonsTexts) {
                        if (activeForm.nextButton.is('input')) {
                            activeForm.nextButton.attr('value', activeForm.settings.nextPrefix + nextTitle + activeForm.settings.nextSufix);
                        } else {
                            activeForm.nextButton.html(activeForm.settings.nextPrefix + nextTitle + activeForm.settings.nextSufix);
                        }
                    }
                    activeForm.nextButton.show();
                } else {
                    activeForm.nextButton.hide();
                }
                if (hasPrev) {
                    activeForm.prevButton.attr('title', prevTitle);
                    if (activeForm.settings.changeButtonsTexts) {
                        if (activeForm.prevButton.is('input')) {
                            activeForm.prevButton.attr('value', activeForm.settings.prevPrefix + prevTitle + activeForm.settings.prevSufix);
                        } else {
                            activeForm.prevButton.html(activeForm.settings.prevPrefix + prevTitle + activeForm.settings.prevSufix);
                        }
                    }
                    activeForm.prevButton.show();
                } else {
                    activeForm.prevButton.hide();
                }
            }
        },
        nextButtonClick: function(event) {
            event.preventDefault();
            var activeForm = $.data($(this)[0], 'activeFormObject');
            if (activeForm instanceof $.activeForm) {
                activeForm.nextPage();
            }
        },
        prevButtonClick: function(event) {
            event.preventDefault();
            var activeForm = $.data($(this)[0], 'activeFormObject');
            if (activeForm instanceof $.activeForm) {
                activeForm.prevPage();
            }
        },
        isIndexInPagesArray: function(index, pagesArray) {
            for (i in pagesArray) {
                if (pagesArray[i].index === index) {
                    return true;
                }
            }
            return false;
        },
        getPrevAndNextPage: function(currentIndex, pagesArray) {
            var output = {
                prev: null,
                next: null
            };
            
            if ($.activeForm.isIndexInPagesArray(currentIndex, pagesArray)) {
                var isNext = false;
                for (index in pagesArray) {
                    if (pagesArray[index].index === currentIndex) {
                        isNext = true;
                    } else {
                        if (isNext) {
                            output.next = pagesArray[index].page;
                            break;
                        } else {
                            output.prev = pagesArray[index].page;
                        }
                    }
                }                
            }
            
            return output;
        },
        getParentForm: function(element) {
            return $(element).parents('form');
        },
        getActiveForm: function(formElement) {
            if ($(formElement).is('form')) {
                return $.data($(formElement)[0], 'activeFormObject');
            }
            return null;
        },
        errorMessage: function(message) {
            var messageToDisplay = 'jQuery.activeForm error: ' + message;
            if (window.console) {
                console.log(messageToDisplay);
            } else {
                alert(messageToDisplay);
            }
        },
        disableInputElementsIn: function(element, hiddenClass) {
            $(element).find('input, textarea, select').each(function() {
                var name = $(this).attr('name');
                $(this).removeAttr('name');
                $(this).attr('hiddenname', name);
                if ($.trim(hiddenClass) !== '') {
                    $(this).addClass($.trim(hiddenClass));
                }
            });
        },
        reenableInputElementsIn: function(element, hiddenClass) {
            $(element).find('input, textarea, select').each(function() {
                var name = $(this).attr('hiddenname');
                $(this).removeAttr('hiddenname');
                $(this).attr('name', name);
                if ($.trim(hiddenClass) !== '') {
                    $(this).removeClass($.trim(hiddenClass));
                }
            });
        }
    });
})( jQuery );