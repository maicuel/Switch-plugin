const { registerBlockType } = wp.blocks;
const { Placeholder } = wp.components;

registerBlockType('mfd/bloque-listado-departamentos', {
    title: 'Listado de Departamentos',
    icon: 'building',
    category: 'widgets',

    edit: () => (
        <Placeholder label="Departamentos">
            Este bloque muestra el listado de departamentos disponibles.
        </Placeholder>
    ),

    save: () => null,
});