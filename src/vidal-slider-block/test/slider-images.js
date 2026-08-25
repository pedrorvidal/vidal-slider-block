import {
	isValidLinkUrl,
	mergeSelectedImages,
	hasAnyInvalidLink,
} from '../slider-images';

describe( 'isValidLinkUrl', () => {
	it( 'accepts a relative link starting with a single slash', () => {
		expect( isValidLinkUrl( '/contato' ) ).toBe( true );
	} );

	it( 'accepts the root path alone', () => {
		expect( isValidLinkUrl( '/' ) ).toBe( true );
	} );

	it( 'accepts an absolute http link', () => {
		expect( isValidLinkUrl( 'http://example.com/page' ) ).toBe( true );
	} );

	it( 'accepts an absolute https link', () => {
		expect( isValidLinkUrl( 'https://example.com/page' ) ).toBe( true );
	} );

	it( 'accepts an uppercase scheme', () => {
		expect( isValidLinkUrl( 'HTTPS://example.com/page' ) ).toBe( true );
	} );

	it( 'rejects a protocol-relative link (open-redirect vector)', () => {
		expect( isValidLinkUrl( '//evil.com' ) ).toBe( false );
	} );

	it( 'rejects a bare domain without a scheme', () => {
		expect( isValidLinkUrl( 'example.com' ) ).toBe( false );
	} );

	it( 'rejects a javascript: scheme', () => {
		expect( isValidLinkUrl( 'javascript:alert(1)' ) ).toBe( false );
	} );

	it( 'rejects an empty string', () => {
		expect( isValidLinkUrl( '' ) ).toBe( false );
	} );
} );

describe( 'mergeSelectedImages', () => {
	it( 'formats a freshly selected image with no previous link', () => {
		const result = mergeSelectedImages(
			[],
			[ { id: 1, url: 'https://example.com/a.jpg', alt: 'A' } ]
		);

		expect( result ).toEqual( [
			{ id: 1, url: 'https://example.com/a.jpg', alt: 'A', link: undefined },
		] );
	} );

	it( 'falls back to an empty alt when the media item has none', () => {
		const result = mergeSelectedImages(
			[],
			[ { id: 1, url: 'https://example.com/a.jpg', alt: '' } ]
		);

		expect( result[ 0 ].alt ).toBe( '' );
	} );

	it( 'preserves the link of an image that was already selected', () => {
		const previousImages = [
			{
				id: 1,
				url: 'https://example.com/a.jpg',
				alt: 'A',
				link: { url: '/contato' },
			},
		];

		const result = mergeSelectedImages( previousImages, [
			{ id: 1, url: 'https://example.com/a.jpg', alt: 'A' },
		] );

		expect( result[ 0 ].link ).toEqual( { url: '/contato' } );
	} );

	it( 'does not attach a link to a newly added image', () => {
		const previousImages = [
			{
				id: 1,
				url: 'https://example.com/a.jpg',
				alt: 'A',
				link: { url: '/contato' },
			},
		];

		const result = mergeSelectedImages( previousImages, [
			{ id: 1, url: 'https://example.com/a.jpg', alt: 'A' },
			{ id: 2, url: 'https://example.com/b.jpg', alt: 'B' },
		] );

		expect( result[ 1 ].link ).toBeUndefined();
	} );

	it( 'drops images that are no longer part of the new selection', () => {
		const previousImages = [
			{ id: 1, url: 'https://example.com/a.jpg', alt: 'A' },
			{ id: 2, url: 'https://example.com/b.jpg', alt: 'B' },
		];

		const result = mergeSelectedImages( previousImages, [
			{ id: 2, url: 'https://example.com/b.jpg', alt: 'B' },
		] );

		expect( result ).toHaveLength( 1 );
		expect( result[ 0 ].id ).toBe( 2 );
	} );
} );

describe( 'hasAnyInvalidLink', () => {
	it( 'is false when no image has a link', () => {
		expect( hasAnyInvalidLink( [ { id: 1 } ] ) ).toBe( false );
	} );

	it( 'is false when every link present is valid', () => {
		const images = [
			{ id: 1, link: { url: '/contato' } },
			{ id: 2, link: { url: 'https://example.com' } },
		];

		expect( hasAnyInvalidLink( images ) ).toBe( false );
	} );

	it( 'is true when at least one link is invalid', () => {
		const images = [
			{ id: 1, link: { url: '/contato' } },
			{ id: 2, link: { url: 'example.com' } },
		];

		expect( hasAnyInvalidLink( images ) ).toBe( true );
	} );

	it( 'is false when a link url is an empty string', () => {
		expect( hasAnyInvalidLink( [ { id: 1, link: { url: '' } } ] ) ).toBe(
			false
		);
	} );

	it( 'is false when a link url is only whitespace', () => {
		expect( hasAnyInvalidLink( [ { id: 1, link: { url: '   ' } } ] ) ).toBe(
			false
		);
	} );
} );
