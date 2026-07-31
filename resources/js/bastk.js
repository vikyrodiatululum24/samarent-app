window.wizard = function () {
    return {
        step: 1,

        next() {
            this.step++;

            window.scrollTo(0, 0);
        },

        prev() {
            this.step--;

            window.scrollTo(0, 0);
        },
    };
};
