(function() {
    const CLASS_PREFIX = 'dvc-depends-on--containerName--';
    let trigger = null;
    let state = {
        containerName: null,
    };

    const checkContainerName = function() {
        state.containerName = trigger.options[trigger.selectedIndex].value;

        render();
    };

    const render = function() {
        if (!state.containerName) {
            return;
        }

        let allDependingFields = document.querySelectorAll('[class*="' + CLASS_PREFIX + '"]');

        for (const field of allDependingFields) {
            let dependencies = Array.from(field.classList).filter(
                (cssClass) => cssClass.indexOf(CLASS_PREFIX) >= 0
            );
            
            if (dependencies.length == 0) {
                continue;
            }

            const dependency = dependencies[0].replace(CLASS_PREFIX, '');
            const displayStyle = (dependency == state.containerName) ? null : 'none';
            field.style.display = displayStyle;
        }
    };

    const init = function() {
        trigger = document.querySelector('[name="containerName"]');
        
        if (!trigger) {
            return;
        }

        trigger.addEventListener('change', checkContainerName);
        checkContainerName();
    };

    if (document.readyState === 'interactive' || document.readyState === 'complete' ) {
        init();
    }
    else {
        document.addEventListener('DOMContentLoaded', init);
    }
})();
