module.exports = {
    "parserOptions": {
        "ecmaVersion": 6,
        "sourceType": "module",
        "ecmaFeatures": {
            "impliedStrict": true,
            "jsx": true
        }
    },
    "env": {
        "browser": true
    },
    "plugins": ["react"],
    "globals": {
        "console": true,
        "setTimeout": true,
        "clearTimeout": true,
        "document": true,
        "Promise": true
    },
    "extends": [
        "eslint:recommended",
        "plugin:react/recommended",
        "plugin:import/warnings",
        "plugin:import/errors"
    ],
    "rules": {
        "complexity": [
            "error",
            11
        ],
        "no-console": [
            "off"
        ],
        "react/default-props-match-prop-types": 1,
        "react/forbid-foreign-prop-types": 1,
        "react/no-unused-prop-types": 1,
        "react/prop-types": 1,
    },
    "settings": {
        "eslint.workingDirectories": ["./js"],
        "react": {
            // default to "createReactClass"
            "pragma": "React",  // Pragma to use, default to "React"
            "version": "detect", // React version. "detect" automatically picks the version you have installed.
        },
        "propWrapperFunctions": [
            // The names of any function used to wrap propTypes, e.g. `forbidExtraProps`. If this isn't set, any propTypes wrapped in a function will be skipped.
            "forbidExtraProps",
            { "property": "freeze", "object": "Object" },
            { "property": "myFavoriteWrapper" }
        ],
        "linkComponents": [
            // Components used as alternatives to <a> for linking, eg. <Link to={ url } />
            "Hyperlink",
            { "name": "Link", "linkAttribute": "to" }
        ]
    }
}
