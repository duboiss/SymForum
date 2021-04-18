import { instantMeiliSearch } from '@meilisearch/instant-meilisearch';

const searchClient = instantMeiliSearch(
    'http://meilisearch:7700',
    null,
    {
        paginationTotalHits: 30, // default: 200.
        placeholderSearch: false, // default: true.
        primaryKey: 'id', // default: undefined
    },
);
