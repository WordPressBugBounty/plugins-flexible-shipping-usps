// Translations are injected by PHP via wp_localize_script into window.wpdeskRatingPetitionL10n
const L10N = (typeof window !== 'undefined' && window.wpdeskRatingPetitionL10n)
  ? window.wpdeskRatingPetitionL10n
  : {};

export const t = (key, fallback) => (
  L10N && Object.prototype.hasOwnProperty.call(L10N, key)
    ? L10N[key]
    : (fallback !== undefined ? fallback : key)
);

export default t;
