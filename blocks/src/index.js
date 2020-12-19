import { registerBlockType } from '@wordpress/blocks';
import { collections } from '@wordpress/api'; 
import apiFetch from '@wordpress/api-fetch';
import { withSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';


async function getEventTitle() {
    const evTitle = (await apiFetch( { path: '/wp/v2/hyic_events' } ))[0].title.rendered;
    console.log(evTitle);
    return <div>{evTitle}</div>;
}
// apiFetch( { path: '/wp/v2/hyic_events' } ).then( posts => {
//     console.log( 'Events', posts );
// } );
// var testVar = posts[0].title.rendered || 'Testtitle';

registerBlockType( 'hyic/events-carousel', {
    title: 'Events-Carousel',
    icon: 'smiley',
    category: 'layout',
    edit: withSelect( ( select ) => {
        return {
            posts: select( 'core' ).getEntityRecords( 'postType', 'hyic_event' ),
        };
    } )( ( { posts } ) => {
        const blockProps = useBlockProps();
        console.log(posts);

        return (
            <div { ...blockProps }>
                { ! posts && 'Loading' }
                { posts && posts.length === 0 && 'No Posts' }
                { posts && posts.length > 0 && (
                    <a href={ posts[ 0 ].link }>
                        { posts[ 0 ].title.rendered }
                    </a>
                ) } 
            </div>
        )
 
    } ),
    //save: () => <div>Hello world</div>,
} );
