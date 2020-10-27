'use strict';

const sortingValue = (selection) => {
    const el = $(selection);
    let value = null;
    if (el) {
        value = el.val();
        if ('-' === value) {
            value = null;
        }
    }
    return value;
};
const getState = () => {
    const filterWrapper = document.querySelector('.filter__list');
    const state = {};
    if (filterWrapper) {
        const filterList = filterWrapper.querySelectorAll('.filter__list-item');
        filterList.forEach(filter => {
            if (filter.classList.contains('active')) {
                state.category = $(filter).data('value');
            }
        });
    }

    const field = sortingValue('.sorting-field');
    const order = sortingValue('.sorting-order');
    if (field && order) {
        state['sortingField'] = field;
        state['sortingOrder'] = order;
    }
    if ((field && !order) || (order && !field)) {
        state.waitEditingSorting = true;
    }

    return state;
};
const reloadHome = (state) => {
    if (state.waitEditingSorting) {
        return;
    }
    const url = new URL(window.location.origin);
    for (let prop in state) {
        if (state.hasOwnProperty(prop)) {
            url.searchParams.set(prop, state[prop]);
        }
    }

    window.location.href = url.href;
};
const checkboxValue = (selection) => {
    const checkbox = $(selection);
    if (checkbox.prop('checked')) {
        return checkbox.val();
    }
    return false;
}
const setCheckboxState = (state, prop, selection) => {
    const value = checkboxValue(selection);
    if (value) {
        state[prop] = value;
    }
    return state;
}
const getFilters = () => {
    const state = {};
    const sliderEl = $('.range__line');

    state.minPrice = sliderEl.slider('values', 0);
    state.maxPrice = sliderEl.slider('values', 1);
    setCheckboxState(state, 'new','#new');
    setCheckboxState(state, 'sale','#sale');

    return state;
};
const filterForm = document.querySelector('.filter-form');
if (filterForm) {
    filterForm.addEventListener('submit', evt => {
        evt.preventDefault();
        const state = getState();
        const filters = getFilters();

        reloadHome({...state, ...filters});
    });
}

const hideSortOptions = e => {
    const el = $(e);
    if (el.val()) {
        return
    }
    el.hide();
};
const changeSortOptions = () => {
    reloadHome(getState());
};
const field = document.querySelector('.sorting-field');
const order = document.querySelector('.sorting-order');
if (field) {
    field.querySelectorAll('option').forEach(hideSortOptions);
    field.addEventListener('change', changeSortOptions);
}
if (order) {
    order.querySelectorAll('option').forEach(hideSortOptions);
    order.addEventListener('change', changeSortOptions);
}
const toggleHidden = (...fields) => {
    fields.forEach((field) => {
        if (!field) return;
        if (field.hidden === true) {
            field.hidden = false;
        } else {
            field.hidden = true;
        }
    });
};

const labelHidden = (form) => {
    form.addEventListener('focusout', (evt) => {
        const field = evt.target;
        const label = field.nextElementSibling;
        if (field.tagName === 'INPUT' && field.value && label) {
            label.hidden = true;
        } else if (label) {
            label.hidden = false;
        }
    });
};

const toggleDelivery = (elem) => {
    const delivery = elem.querySelector('.js-radio');
    const deliveryYes = elem.querySelector('.shop-page__delivery--yes');
    const deliveryNo = elem.querySelector('.shop-page__delivery--no');
    const fields = deliveryYes.querySelectorAll('.custom-form__input');
    const methods = elem.querySelectorAll('.delivery-method-type [type=radio]');

    const requiredToggle = input => {
        input = $(input)
        input.prop('required', !input.prop('required'))
    };
    const fadeEl = el => {
        el.classList.add('fade');
        setTimeout(() => {
            el.classList.remove('fade');
        }, 1000);
    }
    const action = target => {
        let el = 'courier' === target.val() ? deliveryYes : deliveryNo;
        fields.forEach(requiredToggle);
        toggleHidden(deliveryYes, deliveryNo);
        fadeEl(el);
    }
    let target
    methods.forEach(m => {
        if (m.checked) {
            target = $(m);
        }
    });
    if (target) {
        if ('courier' === target.val()) {
            action(target);
        }
    }
    delivery.addEventListener('change', (evt) => {
        const target = $(evt.target);
        action(target);
    });
};

const filterWrapper = document.querySelector('.filter__list');
if (filterWrapper) {
    filterWrapper.addEventListener('click', evt => {
        const filterList = filterWrapper.querySelectorAll('.filter__list-item');
        filterList.forEach(filter => {
            if (filter.classList.contains('active')) {
                filter.classList.remove('active');
            }
        });
        const filter = evt.target;
        filter.classList.add('active');
        reloadHome(getState());
    });
}

const shopList = document.querySelector('.shop__list');
if (shopList) {
    const addButtonOrderClickHandler = (shopOrder, form) => {
        const buttonOrder = shopOrder.querySelector('.button');
        const popupEnd = document.querySelector('.shop-page__popup-end');
        buttonOrder.addEventListener('click', (evt) => {
            form.noValidate = true;
            const inputs = Array.from(shopOrder.querySelectorAll('[required]'));
            inputs.forEach(inp => {
                if (!!inp.value) {
                    if (inp.classList.contains('custom-form__input--error')) {
                        inp.classList.remove('custom-form__input--error');
                    }
                } else {
                    inp.classList.add('custom-form__input--error');
                }
            });

            if (inputs.every(inp => !!inp.value)) {
                evt.preventDefault();
                toggleHidden(shopOrder, popupEnd);
                popupEnd.classList.add('fade');
                setTimeout(() => popupEnd.classList.remove('fade'), 1000);
                window.scroll(0, 0);
                const buttonEnd = popupEnd.querySelector('.button');
                buttonEnd.addEventListener('click', () => {
                    popupEnd.classList.add('fade-reverse');
                    setTimeout(() => {
                        popupEnd.classList.remove('fade-reverse');
                        toggleHidden(popupEnd, document.querySelector('.intro'), document.querySelector('.shop'));
                    }, 1000);
                });

            } else {
                window.scroll(0, 0);
                evt.preventDefault();
            }
        });
    };
    shopList.addEventListener('click', (evt) => {
        if (evt.target.classList.contains('product')) {
            $(evt.target).data('link')
        }

        // const product = evt.path || (evt.composedPath && evt.composedPath());
        // if (prod.some(pathItem => pathItem.classList && pathItem.classList.contains('shop__item'))) {
        //     const shopOrder = document.querySelector('.shop-page__order');
        //     toggleHidden(document.querySelector('.intro'), document.querySelector('.shop'), shopOrder);
        //     window.scroll(0, 0);
        //
        //     shopOrder.classList.add('fade');
        //     setTimeout(() => shopOrder.classList.remove('fade'), 1000);
        //
        //     const form = shopOrder.querySelector('.custom-form');
        //     labelHidden(form);
        //
        //     toggleDelivery(shopOrder);
        //     // addButtonOrderClickHandler(shopOrder, form)
        // }
    });
}
const shopOrder = document.querySelector('.shop-page__order');
if (shopOrder) {
    toggleDelivery(shopOrder);
}
const pageOrderList = document.querySelector('.page-order__list');
if (pageOrderList) {
    pageOrderList.addEventListener('click', evt => {
        if (evt.target.classList && evt.target.classList.contains('order-item__toggle')) {
            const path = evt.path || (evt.composedPath && evt.composedPath());
            Array.from(path).forEach(element => {
                if (element.classList && element.classList.contains('page-order__item')) {
                    element.classList.toggle('order-item--active');
                }
            });
            evt.target.classList.toggle('order-item__toggle--active');
        }

        if (evt.target.classList && evt.target.classList.contains('order-item__btn')) {
            const status = evt.target.previousElementSibling;
            if (status.classList && status.classList.contains('order-item__info--no')) {
                status.textContent = 'Выполнено';
            } else {
                status.textContent = 'Не выполнено';
            }

            status.classList.toggle('order-item__info--no');
            status.classList.toggle('order-item__info--yes');
        }
    });
}

const checkList = (list, btn) => {
    if (list.children.length === 1) {
        btn.hidden = false;
    } else {
        btn.hidden = true;
    }
};
const addList = document.querySelector('.add-list');
if (addList) {
    const form = document.querySelector('.custom-form');
    labelHidden(form);

    const addButton = addList.querySelector('.add-list__item--add');
    const addInput = addList.querySelector('.product-photo-input');

    checkList(addList, addButton);
    const addButtonOrderClickHandler = (form) => {
        const button = document.querySelector('.button');
        const popupEnd = document.querySelector('.page-add__popup-end');
        button.addEventListener('click', (evt) => {
            evt.preventDefault();
            form.hidden = true;
            popupEnd.hidden = false;
        });
    };
    addInput.addEventListener('change', evt => {
        const template = document.createElement('LI');
        const img = document.createElement('IMG');

        template.className = 'add-list__item add-list__item--active';
        template.addEventListener('click', evt => {
            addList.removeChild(evt.target);
            addInput.value = '';
            checkList(addList, addButton);
        });

        const file = evt.target.files[0];
        const reader = new FileReader();

        reader.onload = (evt) => {
            img.src = evt.target.result;
            template.appendChild(img);
            addList.appendChild(template);
            checkList(addList, addButton);
        };

        reader.readAsDataURL(file);
    });

    // addButtonOrderClickHandler(form);

}

const productsList = document.querySelector('.page-products__list');
if (productsList) {
    productsList.addEventListener('click', evt => {
        const target = evt.target;
        if (target.classList && target.classList.contains('product-item__delete')) {
            productsList.removeChild(target.parentElement);
        }
    });
}

// jquery range maxmin
if (document.querySelector('.shop-page')) {
    const min = $('.min-price');
    const max = $('.max-price');
    $('.range__line').slider({
        min: min.data('limit'),
        max: max.data('limit'),
        values: [
            min.data('value'),
            max.data('value'),
        ],
        range: true,
        stop: function (event, ui) {
            $('.min-price').text($('.range__line').slider('values', 0) + ' руб.');
            $('.max-price').text($('.range__line').slider('values', 1) + ' руб.');
        },
        slide: function (event, ui) {
            $('.min-price').text($('.range__line').slider('values', 0) + ' руб.');
            $('.max-price').text($('.range__line').slider('values', 1) + ' руб.');
        }
    });
}
