function toggle(elem) {
    elem = document.getElementById(elem);

    if (elem.style && elem.style['display'])
    {
        // Only works with the "style" attr
        var disp = elem.style['display'];
    }
    else if (window.getComputedStyle)
    {
        // For most other browsers
        var disp = document.defaultView.getComputedStyle(elem, null).getPropertyValue('display');
    }

    // Toggle the state of the "display" style
    elem.style.display = disp == 'block' ? 'none' : 'block';

    return false;
}

document.addEventListener('DOMContentLoaded', function() {
    var anchors = document.querySelectorAll('#tabs a');

    anchors.forEach(function(anchor) {
        anchor.addEventListener('click', function (event) {
            event.stopPropagation();

            // Remove 'active' class from all anchors
            anchors.forEach(function(a) {
                a.classList.remove('active');
            });

            // Add 'active' class to the clicked anchor
            event.target.classList.add('active');

            // Get the target tab
            tabName = event.target.dataset.tab;

            // Select all div elements that are direct children of the element with ID 'tab-content'
            var tabContentDivs = document.querySelectorAll('#tab-content > div');

            // Iterate over the NodeList and set display: none;
            tabContentDivs.forEach(function(div) {
                div.style.display = 'none';
            });

            var activeTab = document.getElementById(tabName);
            if (activeTab) {
                activeTab.style.display = 'block';
            }
        });
    });
});
