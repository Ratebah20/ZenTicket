<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    '@api-platform/admin' => [
        'version' => '4.0.4',
    ],
    'react' => [
        'version' => '19.0.0',
    ],
    'react-admin' => [
        'version' => '5.5.3',
    ],
    '@tanstack/react-query' => [
        'version' => '5.66.9',
    ],
    'lodash.isplainobject' => [
        'version' => '4.0.6',
    ],
    'query-string' => [
        'version' => '9.1.1',
    ],
    'react-router-dom' => [
        'version' => '7.2.0',
    ],
    '@mui/material' => [
        'version' => '6.4.5',
    ],
    '@mui/icons-material/ExpandMore' => [
        'version' => '6.4.5',
    ],
    '@mui/icons-material/History' => [
        'version' => '6.4.5',
    ],
    '@mui/icons-material/Refresh' => [
        'version' => '6.4.5',
    ],
    '@api-platform/api-doc-parser' => [
        'version' => '0.16.7',
    ],
    'jsonld' => [
        'version' => '8.3.3',
    ],
    'ra-core' => [
        'version' => '5.5.3',
    ],
    'ra-ui-materialui' => [
        'version' => '5.5.3',
    ],
    'ra-language-english' => [
        'version' => '5.5.3',
    ],
    'ra-i18n-polyglot' => [
        'version' => '5.5.3',
    ],
    '@tanstack/query-core' => [
        'version' => '5.66.4',
    ],
    'react/jsx-runtime' => [
        'version' => '19.0.0',
    ],
    'decode-uri-component' => [
        'version' => '0.4.1',
    ],
    'filter-obj' => [
        'version' => '5.1.0',
    ],
    'split-on-first' => [
        'version' => '3.0.0',
    ],
    'react-router/dom' => [
        'version' => '7.2.0',
    ],
    'react-router' => [
        'version' => '7.2.0',
    ],
    '@mui/utils/capitalize' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/createChainedFunction' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/debounce' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/deprecatedPropType' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/isMuiElement' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/ownerDocument' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/ownerWindow' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/requirePropFactory' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/setRef' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/useEnhancedEffect' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/useId' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/unsupportedProp' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/useControlled' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/useEventCallback' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/useForkRef' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/getScrollbarSize' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/formatMuiErrorMessage' => [
        'version' => '6.4.3',
    ],
    '@mui/system' => [
        'version' => '6.4.3',
    ],
    '@mui/system/createBreakpoints' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/deepmerge' => [
        'version' => '6.4.3',
    ],
    '@mui/system/colorManipulator' => [
        'version' => '6.4.3',
    ],
    '@mui/system/spacing' => [
        'version' => '6.4.3',
    ],
    '@mui/system/cssVars' => [
        'version' => '6.4.3',
    ],
    '@mui/system/styleFunctionSx' => [
        'version' => '6.4.3',
    ],
    '@mui/system/createTheme' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/generateUtilityClass' => [
        'version' => '6.4.3',
    ],
    '@mui/system/useThemeProps' => [
        'version' => '6.4.3',
    ],
    '@mui/system/createStyled' => [
        'version' => '6.4.3',
    ],
    '@mui/system/InitColorSchemeScript' => [
        'version' => '6.4.3',
    ],
    '@mui/utils' => [
        'version' => '6.4.3',
    ],
    'prop-types' => [
        'version' => '15.8.1',
    ],
    'clsx' => [
        'version' => '2.1.1',
    ],
    '@mui/utils/composeClasses' => [
        'version' => '6.4.3',
    ],
    '@mui/system/DefaultPropsProvider' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/generateUtilityClasses' => [
        'version' => '6.4.3',
    ],
    'react-is' => [
        'version' => '19.0.0',
    ],
    '@mui/utils/chainPropTypes' => [
        'version' => '6.4.3',
    ],
    'react-transition-group' => [
        'version' => '4.4.5',
    ],
    '@mui/utils/useTimeout' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/elementTypeAcceptingRef' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/integerPropType' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/appendOwnerState' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/resolveComponentProps' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/mergeSlotProps' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/refType' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/isFocusVisible' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/useLazyRef' => [
        'version' => '6.4.3',
    ],
    '@mui/material/utils' => [
        'version' => '6.4.5',
    ],
    '@mui/system/RtlProvider' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/HTMLElementType' => [
        'version' => '6.4.3',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    '@mui/utils/useSlotProps' => [
        'version' => '6.4.3',
    ],
    'react-dom' => [
        'version' => '19.0.0',
    ],
    '@mui/utils/elementAcceptingRef' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/getReactElementRef' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/usePreviousProps' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/resolveProps' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/getValidReactChildren' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/extractEventHandlers' => [
        'version' => '6.4.3',
    ],
    '@mui/system/Grid' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/exactProp' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/getDisplayName' => [
        'version' => '6.4.3',
    ],
    '@mui/system/useMediaQuery' => [
        'version' => '6.4.3',
    ],
    '@mui/system/style' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/clamp' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/visuallyHidden' => [
        'version' => '6.4.3',
    ],
    'tslib' => [
        'version' => '2.8.1',
    ],
    'graphql/utilities/index.js' => [
        'version' => '16.10.0',
    ],
    'lodash.get' => [
        'version' => '4.4.2',
    ],
    'jsonref' => [
        'version' => '8.0.8',
    ],
    'inflection' => [
        'version' => '1.13.4',
    ],
    'rdf-canonize' => [
        'version' => '3.4.0',
    ],
    'lru-cache' => [
        'version' => '6.0.0',
    ],
    'canonicalize' => [
        'version' => '1.0.8',
    ],
    'lodash/set' => [
        'version' => '4.17.21',
    ],
    'lodash/unset' => [
        'version' => '4.17.21',
    ],
    'lodash/get' => [
        'version' => '4.17.21',
    ],
    'lodash/isEqual' => [
        'version' => '4.17.21',
    ],
    'lodash/debounce' => [
        'version' => '4.17.21',
    ],
    'eventemitter3' => [
        'version' => '5.0.1',
    ],
    'lodash/pick' => [
        'version' => '4.17.21',
    ],
    'react-error-boundary' => [
        'version' => '4.1.2',
    ],
    'lodash/merge' => [
        'version' => '4.17.21',
    ],
    'lodash/cloneDeep' => [
        'version' => '4.17.21',
    ],
    'lodash/defaults' => [
        'version' => '4.17.21',
    ],
    'lodash/union' => [
        'version' => '4.17.21',
    ],
    'react-hook-form' => [
        'version' => '7.54.2',
    ],
    'jsonexport/dist' => [
        'version' => '3.2.0',
    ],
    'lodash/memoize' => [
        'version' => '4.17.21',
    ],
    'date-fns' => [
        'version' => '3.6.0',
    ],
    '@mui/icons-material/Lock' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Queue' => [
        'version' => '6.4.2',
    ],
    '@mui/material/styles' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Add' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/RemoveRedEye' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Sort' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/ArrowDropDown' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Delete' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Menu' => [
        'version' => '6.4.2',
    ],
    '@mui/material/CircularProgress' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/GetApp' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Update' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/WarningAmber' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Settings' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/DragIndicator' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/CancelOutlined' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/DeleteOutline' => [
        'version' => '6.4.2',
    ],
    '@mui/material/CardContent' => [
        'version' => '6.4.2',
    ],
    '@mui/material/Dialog' => [
        'version' => '6.4.2',
    ],
    '@mui/material/DialogActions' => [
        'version' => '6.4.2',
    ],
    '@mui/material/DialogContent' => [
        'version' => '6.4.2',
    ],
    '@mui/material/DialogContentText' => [
        'version' => '6.4.2',
    ],
    '@mui/material/DialogTitle' => [
        'version' => '6.4.2',
    ],
    '@mui/material/Button' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/CheckCircle' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/ErrorOutline' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Dashboard' => [
        'version' => '6.4.2',
    ],
    'css-mediaquery' => [
        'version' => '0.1.2',
    ],
    '@mui/icons-material/Report' => [
        'version' => '6.4.2',
    ],
    '@mui/material/useScrollTrigger' => [
        'version' => '6.4.2',
    ],
    '@mui/material/Slide' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/ViewList' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/HotTub' => [
        'version' => '6.4.2',
    ],
    '@mui/material/Toolbar' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/AccountCircle' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/PowerSettingsNew' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Create' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Translate' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/List' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/NavigateBefore' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/NavigateNext' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Error' => [
        'version' => '6.4.2',
    ],
    '@mui/material/Tooltip' => [
        'version' => '6.4.2',
    ],
    '@mui/material/IconButton' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Save' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Brightness4' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Brightness7' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/RemoveCircleOutline' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/ArrowCircleUp' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/ArrowCircleDown' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/AddCircleOutline' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/HighlightOff' => [
        'version' => '6.4.2',
    ],
    '@mui/material/FormControlLabel' => [
        'version' => '6.4.2',
    ],
    '@mui/material/FormHelperText' => [
        'version' => '6.4.2',
    ],
    '@mui/material/FormGroup' => [
        'version' => '6.4.2',
    ],
    '@mui/material/Switch' => [
        'version' => '6.4.2',
    ],
    '@mui/material/FormLabel' => [
        'version' => '6.4.2',
    ],
    '@mui/material/FormControl' => [
        'version' => '6.4.2',
    ],
    '@mui/material/Checkbox' => [
        'version' => '6.4.2',
    ],
    '@mui/material/TableCell' => [
        'version' => '6.4.2',
    ],
    'lodash/difference' => [
        'version' => '4.17.21',
    ],
    '@mui/material/Typography' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Close' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/ViewWeek' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Clear' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/BookmarkAdd' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/BookmarkRemove' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/BookmarkBorder' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/FilterList' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/CheckBoxOutlineBlank' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/CheckBox' => [
        'version' => '6.4.2',
    ],
    'lodash/matches' => [
        'version' => '4.17.21',
    ],
    'lodash/pickBy' => [
        'version' => '4.17.21',
    ],
    '@mui/icons-material/Search' => [
        'version' => '6.4.2',
    ],
    '@mui/material/TextField' => [
        'version' => '6.4.2',
    ],
    'react-dropzone' => [
        'version' => '14.3.5',
    ],
    '@mui/icons-material/RemoveCircle' => [
        'version' => '6.4.2',
    ],
    '@mui/material/MenuItem' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Visibility' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/VisibilityOff' => [
        'version' => '6.4.2',
    ],
    '@mui/material/Radio' => [
        'version' => '6.4.2',
    ],
    '@mui/material/Tab' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/HelpOutline' => [
        'version' => '6.4.2',
    ],
    '@mui/material/Tabs' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Done' => [
        'version' => '6.4.2',
    ],
    '@mui/material/Chip' => [
        'version' => '6.4.2',
    ],
    'dompurify' => [
        'version' => '2.5.8',
    ],
    '@mui/material/AppBar' => [
        'version' => '6.4.2',
    ],
    '@mui/icons-material/Inbox' => [
        'version' => '6.4.2',
    ],
    '@mui/material/Card' => [
        'version' => '6.4.2',
    ],
    '@mui/material/Avatar' => [
        'version' => '6.4.2',
    ],
    'node-polyglot' => [
        'version' => '2.6.0',
    ],
    'turbo-stream' => [
        'version' => '2.4.0',
    ],
    'cookie' => [
        'version' => '1.0.2',
    ],
    'set-cookie-parser' => [
        'version' => '2.7.1',
    ],
    '@mui/styled-engine' => [
        'version' => '6.4.3',
    ],
    '@mui/utils/ClassNameGenerator' => [
        'version' => '6.4.3',
    ],
    '@mui/private-theming' => [
        'version' => '6.4.3',
    ],
    '@babel/runtime/helpers/esm/extends' => [
        'version' => '7.23.8',
    ],
    '@babel/runtime/helpers/esm/objectWithoutPropertiesLoose' => [
        'version' => '7.23.8',
    ],
    '@babel/runtime/helpers/esm/inheritsLoose' => [
        'version' => '7.23.8',
    ],
    'dom-helpers/addClass' => [
        'version' => '5.2.1',
    ],
    'dom-helpers/removeClass' => [
        'version' => '5.2.1',
    ],
    '@babel/runtime/helpers/esm/assertThisInitialized' => [
        'version' => '7.23.8',
    ],
    'setimmediate' => [
        'version' => '1.0.5',
    ],
    'yallist' => [
        'version' => '4.0.0',
    ],
    'file-selector' => [
        'version' => '2.1.0',
    ],
    'attr-accept' => [
        'version' => '2.2.5',
    ],
    'object.entries' => [
        'version' => '1.1.8',
    ],
    'warning' => [
        'version' => '4.0.3',
    ],
    'hasown' => [
        'version' => '2.0.2',
    ],
    '@emotion/styled' => [
        'version' => '11.14.0',
    ],
    '@emotion/serialize' => [
        'version' => '1.3.3',
    ],
    '@emotion/react' => [
        'version' => '11.14.0',
    ],
    '@emotion/cache' => [
        'version' => '11.14.0',
    ],
    '@emotion/sheet' => [
        'version' => '1.4.0',
    ],
    'define-properties' => [
        'version' => '1.2.1',
    ],
    'call-bind' => [
        'version' => '1.0.7',
    ],
    'es-object-atoms/RequireObjectCoercible' => [
        'version' => '1.0.0',
    ],
    'call-bind/callBound' => [
        'version' => '1.0.7',
    ],
    'function-bind' => [
        'version' => '1.1.2',
    ],
    '@emotion/use-insertion-effect-with-fallbacks' => [
        'version' => '1.2.0',
    ],
    '@emotion/utils' => [
        'version' => '1.4.2',
    ],
    '@emotion/is-prop-valid' => [
        'version' => '1.3.1',
    ],
    '@babel/runtime/helpers/extends' => [
        'version' => '7.26.0',
    ],
    '@emotion/hash' => [
        'version' => '0.9.2',
    ],
    '@emotion/unitless' => [
        'version' => '0.10.0',
    ],
    '@emotion/memoize' => [
        'version' => '0.9.0',
    ],
    '@emotion/weak-memoize' => [
        'version' => '0.4.0',
    ],
    'hoist-non-react-statics' => [
        'version' => '3.3.2',
    ],
    'stylis' => [
        'version' => '4.2.0',
    ],
    'object-keys' => [
        'version' => '1.1.1',
    ],
    'define-data-property' => [
        'version' => '1.1.1',
    ],
    'has-property-descriptors' => [
        'version' => '1.0.1',
    ],
    'get-intrinsic' => [
        'version' => '1.2.4',
    ],
    'set-function-length' => [
        'version' => '1.2.1',
    ],
    'es-errors/type' => [
        'version' => '1.3.0',
    ],
    'es-define-property' => [
        'version' => '1.0.0',
    ],
    'gopd' => [
        'version' => '1.0.1',
    ],
    'es-errors' => [
        'version' => '1.3.0',
    ],
    'es-errors/eval' => [
        'version' => '1.3.0',
    ],
    'es-errors/range' => [
        'version' => '1.3.0',
    ],
    'es-errors/ref' => [
        'version' => '1.3.0',
    ],
    'es-errors/syntax' => [
        'version' => '1.3.0',
    ],
    'es-errors/uri' => [
        'version' => '1.3.0',
    ],
    'has-symbols' => [
        'version' => '1.0.3',
    ],
    'has-proto' => [
        'version' => '1.0.1',
    ],
];
