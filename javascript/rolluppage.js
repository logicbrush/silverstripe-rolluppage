document.addEventListener('DOMContentLoaded', function() {
    let rollupTabLinks = document.querySelectorAll('.typography .rollup-page-navigation-tabs li a');

    let changeRollupTab = function() {
        let activeRollupTabLinks = document.querySelectorAll(
            '.typography .rollup-page-navigation-tabs li.active, .rollup-page-content.active'
        );

        let rollupPageContent = document.getElementById('rollup-page-content-' + this.dataset.urlSegment);

        if (rollupPageContent) {
            [].forEach.call(activeRollupTabLinks, function(activeRollupTabLink) {
                activeRollupTabLink.classList.remove('active');
            });

            this.parentElement.classList.add('active');
            rollupPageContent.classList.add('active');
        }
    };

    for (let i = 0; i < rollupTabLinks.length; i++) {
        rollupTabLinks[i].addEventListener('click', changeRollupTab, false);
    }
}, false);
